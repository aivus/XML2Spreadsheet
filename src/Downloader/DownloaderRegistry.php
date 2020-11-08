<?php

namespace aivus\XML2Spreadsheet\Downloader;

use Psr\Log\LoggerInterface;

/**
 * Registry of all known dowloaders
 */
class DownloaderRegistry
{
    /** @var DownloaderInterface[] */
    private array $downloaders = [];
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function addDownloader(DownloaderInterface $downloader)
    {
        $this->downloaders[] = $downloader;
    }

    /**
     * Get an array of downloaders which support this schema
     *
     * @param string|null $schema
     * @return DownloaderInterface[]
     */
    public function getSupportedDownloaders($schema): array
    {
        $supportedDownloaders = [];
        foreach ($this->downloaders as $downloader) {
            $downloaderClass = get_class($downloader);
            if ($downloader->isSchemaSupported($schema)) {
                $this->logger->debug('Downloader {downloaderClass} supports schema {schema}',
                    [
                        'downloaderClass' => $downloaderClass,
                        'schema' => $schema
                    ]
                );
                $supportedDownloaders[] = $downloader;
            } else {
                $this->logger->debug('Downloader {downloaderClass} skipped because it does not support schema {schema}',
                    [
                        'downloaderClass' => $downloaderClass,
                        'schema' => $schema
                    ]
                );
            }
        }

        return $supportedDownloaders;
    }
}
