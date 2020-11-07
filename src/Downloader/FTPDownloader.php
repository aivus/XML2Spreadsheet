<?php

namespace aivus\XML2Spreadsheet\Downloader;

use Psr\Http\Message\ResponseInterface;

class FTPDownloader implements DownloaderInterface
{
    public function getFileByURI(string $uri): ResponseInterface
    {
    }

    public function isSchemaSupported(string $schema): bool
    {
        return false;
    }
}
