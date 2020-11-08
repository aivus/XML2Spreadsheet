<?php

namespace aivus\XML2Spreadsheet\Downloader;

use aivus\XML2Spreadsheet\Context;

/**
 * Interface for downloader which can download files
 */
interface DownloaderInterface
{
    /**
     * Get file resource by specified URI
     *
     * @param string $uri URI of the file
     * @param Context $context Additional data for the downloader
     * @return resource|false Returns PHP resource of the opened file with downloaded content, false otherwise
     */
    public function getFileByURI(string $uri, Context $context);

    /**
     * Check specified schema is supported by downloader
     *
     * @param string|null $schema
     */
    public function isSchemaSupported($schema): bool;
}
