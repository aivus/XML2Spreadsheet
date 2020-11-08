<?php

namespace aivus\XML2Spreadsheet\Handler;

use aivus\XML2Spreadsheet\Converter\ProductsupXMLConverter;
use aivus\XML2Spreadsheet\Downloader\DownloaderInterface;
use aivus\XML2Spreadsheet\Downloader\DownloaderRegistry;
use aivus\XML2Spreadsheet\Exception\DownloadSourceFileException;
use aivus\XML2Spreadsheet\Exception\SupportedDownloaderNotFound;

class ConvertHandler
{
    private ProductsupXMLConverter $converter;
    private DownloaderRegistry $downloaderRegistry;

    public function convert(string $uri, array $context)
    {
        $file = $this->downloadFile($uri, $context);
        try {
            $this->converter->convert($file);
        } finally {
            if (is_resource($file)) {
                fclose($file);
            }
        }

    }

    public function __construct(ProductsupXMLConverter $converter, DownloaderRegistry $downloaderRegistry)
    {
        $this->converter = $converter;
        $this->downloaderRegistry = $downloaderRegistry;
    }

    /**
     * @return resource
     */
    private function downloadFile(string $uri, array $context)
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
                var_dump($e->getMessage());
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
}
