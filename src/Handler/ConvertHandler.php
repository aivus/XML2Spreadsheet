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

/**
 * Facade for all convertion logic
 */
class ConvertHandler
{
    private ConverterFactory $converterFactory;
    private DownloaderRegistry $downloaderRegistry;
    private ContainerInterface $container;

    public function __construct(
        ConverterFactory $converterFactory,
        DownloaderRegistry $downloaderRegistry,
        ContainerInterface $container
    ) {
        $this->converterFactory = $converterFactory;
        $this->downloaderRegistry = $downloaderRegistry;
        $this->container = $container;
    }

    public function convert(string $uri, Context $context)
    {
        $file = $this->downloadFile($uri, $context);

        $parser = $this->getParser($context);
        $converter = $this->converterFactory->create();
        $converter->setParser($parser);

        try {
            $spreadsheetInfo = $converter->convert($file, $context);
        } catch (\Exception $e) {
            // TODO: Add logging
            throw $e;
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

        foreach ($downloaders as $downloader) {
            try {
                $file = $downloader->getFileByURI($uri, $context);
            } catch (\Exception $e) {
                // TODO: Log it
                continue;
            }

            if ($file) {
                return $file;
            }
        }

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
        try {
            return $this->container->get('parser.' . $parserName);
        } catch (NotFoundExceptionInterface $e) {
            throw new ParserNotFound(sprintf('Parser with name "%s" cannot be found', $parserName), 0, $e);
        }
    }
}
