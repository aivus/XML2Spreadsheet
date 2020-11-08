<?php

namespace aivus\XML2Spreadsheet\Handler;

use aivus\XML2Spreadsheet\Context;
use aivus\XML2Spreadsheet\Converter\ConverterFactory;
use aivus\XML2Spreadsheet\Downloader\DownloaderInterface;
use aivus\XML2Spreadsheet\Downloader\DownloaderRegistry;
use aivus\XML2Spreadsheet\Exception\DownloadSourceFileException;
use aivus\XML2Spreadsheet\Exception\ParserNotFound;
use aivus\XML2Spreadsheet\Exception\SupportedDownloaderNotFound;
use aivus\XML2Spreadsheet\Parser\ParserInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;

/**
 * Facade for all convertion logic
 */
class ConvertHandler
{
    private ConverterFactory $converterFactory;
    private DownloaderRegistry $downloaderRegistry;
    private ContainerInterface $container;
    private LoggerInterface $logger;

    public function __construct(
        ConverterFactory $converterFactory,
        DownloaderRegistry $downloaderRegistry,
        ContainerInterface $container,
        LoggerInterface $logger
    ) {
        $this->converterFactory = $converterFactory;
        $this->downloaderRegistry = $downloaderRegistry;
        $this->container = $container;
        $this->logger = $logger;
    }

    public function convert(string $uri, Context $context)
    {
        $file = $this->downloadFile($uri, $context);

        if (!is_resource($file)) {
            throw new \RuntimeException('Downloaded file is not a valid resource');
        }

        $parser = $this->getParser($context);
        $converter = $this->converterFactory->create();
        $converter->setParser($parser);

        try {
            $spreadsheetInfo = $converter->convert($file, $context);
        } finally {
            if (is_resource($file)) {
                fclose($file);
            }
        }

        return $spreadsheetInfo;
    }

    /**
     * @return resource
     */
    private function downloadFile(string $uri, Context $context)
    {
        $downloaders = $this->getDownloaders($uri);

        if (!$downloaders) {
            throw new SupportedDownloaderNotFound('Can not find supported downloader for specified URI');
        }

        $this->logger->info('Starting download file {file}', ['file' => $uri]);

        foreach ($downloaders as $downloader) {
            $this->logger->debug(
                'Trying to download using {downloadClass}',
                ['downloadClass' => get_class($downloader)]
            );
            try {
                $file = $downloader->getFileByURI($uri, $context);
            } catch (\Exception $e) {
                $this->logger->error(
                    'Downloading failed. Continue to the next available downloader',
                    ['exception' => $e]
                );
                continue;
            }

            if ($file) {
                $this->logger->info('File {file} successfully downloaded', ['file' => $uri]);

                return $file;
            }
        }

        $this->logger->error('Can not download file {file}', ['file' => $uri]);

        throw new DownloadSourceFileException('Can not receive source file');
    }

    /**
     * @return DownloaderInterface[]
     */
    private function getDownloaders(string $uri): array
    {
        $schema = parse_url($uri, PHP_URL_SCHEME);

        return $this->downloaderRegistry->getSupportedDownloaders($schema);
    }

    private function getParser(Context $context): ParserInterface
    {
        $parserName = $context->getParserName();
        $this->logger->debug('Getting parser with name "{parserName}" from the container', ['parserName' => $parserName]);

        try {
            return $this->container->get('parser.' . $parserName);
        } catch (NotFoundExceptionInterface $e) {
            throw new ParserNotFound(sprintf('Parser with name "%s" cannot be found', $parserName), 0, $e);
        }
    }
}
