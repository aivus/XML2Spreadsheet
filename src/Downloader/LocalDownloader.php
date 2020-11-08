<?php

namespace aivus\XML2Spreadsheet\Downloader;

class LocalDownloader implements DownloaderInterface
{
    public function getFileByURI(string $uri, array $context = [])
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
