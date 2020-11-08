<?php

namespace aivus\XML2Spreadsheet\Downloader;

interface DownloaderInterface
{
    /**
     * Get file php resource by specified URI
     *
     * @param string $uri URI of the file
     * @param array $context Additional data for the downloader
     * @return resource|false Returns PHP resource of the opened file with downloaded content, false otherwise
     */
    public function getFileByURI(string $uri, array $context = []);

    /**
     * Check specified schema is supported by downloader
     * @param string|null $schema
     */
    public function isSchemaSupported($schema): bool;
}
