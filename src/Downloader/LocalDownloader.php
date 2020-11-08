<?php

namespace aivus\XML2Spreadsheet\Downloader;

use aivus\XML2Spreadsheet\Context;
use Psr\Log\LoggerInterface;

/**
 * Downloader for local files
 * It supports files:// protocol and local path
 */
class LocalDownloader implements DownloaderInterface
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function getFileByURI(string $uri, Context $context)
    {
        if (!is_readable($uri)) {
            $this->logger->warning('Can not proceed. File {file} is not readable (or not exist)', ['file' => $uri]);
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
