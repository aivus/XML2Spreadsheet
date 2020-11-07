<?php

namespace aivus\XML2Spreadsheet\Downloader;

interface DownloaderInterface
{
    /**
     * Get file php resource by specified URI
     *
     * @return resource
     */
    public function getFileByURI(string $uri);

    /**
     * Check specified schema is supported by downloader
     */
    public function isSchemaSupported(string $schema): bool;
}
