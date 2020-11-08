<?php

namespace aivus\XML2Spreadsheet\Downloader;

use aivus\XML2Spreadsheet\Context;

/**
 * Downloader for local files
 * It supports files:// protocol and local path
 */
class LocalDownloader implements DownloaderInterface
{
    public function getFileByURI(string $uri, Context $context)
    {
        if (!is_readable($uri)) {
            return false;
        }

        return fopen($uri, 'r');
    }

    public function isSchemaSupported($schema): bool
    {
        if ($schema === null) {
            return true;
        }

        return strtolower($schema) === 'file';
    }
}
