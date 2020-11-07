<?php

namespace aivus\XML2Spreadsheet\Downloader;

class DownloaderRegistry
{
    private array $downloaders = [];

    public function addDownloader(DownloaderInterface $downloader)
    {
        $this->downloaders[] = $downloader;
    }

    /**
     * Get an array of downloaders which support this schema
     *
     * @param string $schema
     * @return DownloaderInterface[]
     */
    public function getSupportedDownloaders(string $schema): array
    {
        $supportedDownloaders = [];
        foreach ($this->downloaders as $downloader) {
            if ($downloader->isSchemaSupported($schema)) {
                $supportedDownloaders[] = $downloader;
            }
        }

        return $supportedDownloaders;
    }
}
