<?php

namespace aivus\XML2Spreadsheet\Downloader;

use aivus\XML2Spreadsheet\Context;
use aivus\XML2Spreadsheet\Exception\DownloadSourceFileException;
use FtpClient\FtpClient;
use Psr\Log\LoggerInterface;

/**
 * Downloader for FTP files
 * It supports ftp protocol
 */
class FTPDownloader implements DownloaderInterface
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function getFileByURI(string $uri, Context $context)
    {
        $uriComponents = parse_url($uri);

        if (!array_key_exists('host', $uriComponents)) {
            throw new DownloadSourceFileException('Can not download a file using FTP. Hostname is missed in URI');
        }

        $ftp = new FtpClient();
        $ftp->connect($uriComponents['host']);

        $username = $uriComponents['user'] ?? 'anonymous';
        $password = $uriComponents['pass'] ?? '';
        $ftp->login($username, $password);

        if (!array_key_exists('path', $uriComponents)) {
            throw new DownloadSourceFileException(
                'Can not download a file using FTP. Path to the file is missed in URI'
            );
        }

        $this->logger->debug('Downloading file {path} from {host}',
            ['path' => $uriComponents['path'], 'host' => $uriComponents['host']]
        );

        $file = tmpfile();
        $result = $ftp->fget($file, $uriComponents['path']);
        $ftp->close();

        if (!$result) {
            $this->logFailedDownload();
            fclose($file);

            return false;
        }

        return $file;
    }

    public function isSchemaSupported($schema): bool
    {
        return strtolower($schema) === 'ftp';
    }

    private function logFailedDownload()
    {
        $lastError = error_get_last();
        if ($lastError) {
            $this->logger->warning('Downloading failed. Error: "{message}"',
                ['lastError' => $lastError, 'message' => $lastError['message']]
            );
        }
    }
}
