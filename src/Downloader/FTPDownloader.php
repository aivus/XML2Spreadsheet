<?php

namespace aivus\XML2Spreadsheet\Downloader;

use aivus\XML2Spreadsheet\Exception\DownloadSourceFileException;
use FtpClient\FtpClient;

class FTPDownloader implements DownloaderInterface
{
    public function getFileByURI(string $uri, array $context = [])
    {
        $uriComponents = parse_url($uri);

        if (!array_key_exists('host', $uriComponents)) {
            throw new DownloadSourceFileException('Can not download file');
        }

        $ftp = new FtpClient();
        $ftp->connect($uriComponents['host']);
        // TODO: cover the case when user/pass/path not provided
        $ftp->login($uriComponents['user'], $uriComponents['pass']);

        $file = tmpfile();
        $ftp->fget($file, $uriComponents['path']);
        $ftp->close();

        return $file;
    }

    public function isSchemaSupported($schema): bool
    {
        return strtolower($schema) === 'ftp';
    }
}
