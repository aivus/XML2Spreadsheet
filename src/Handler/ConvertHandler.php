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

    public function __construct(ProductsupXMLConverter $converter, DownloaderRegistry $downloaderRegistry)
    {
        $this->converter = $converter;
        $this->downloaderRegistry = $downloaderRegistry;
    }

    public function convert(string $uri)
    {
        $file = $this->downloadFile($uri);
        $this->converter->convert($file);
    }

    /**
     * @param string $uri
     * @return resource
     */
    private function downloadFile(string $uri)
    {
        $downloaders = $this->getDownloaders($uri);

        if (!$downloaders) {
            throw new SupportedDownloaderNotFound('Can not find supported downloader for specified URI');
        }

        foreach ($downloaders as $downloader) {
            $file = $downloader->getFileByURI($uri);
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
