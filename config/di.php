<?php

use aivus\XML2Spreadsheet\Downloader\{DownloaderRegistry, FTPDownloader, HTTPDownloader, LocalDownloader};

return [
    DownloaderRegistry::class => DI\create()
        ->method('addDownloader', DI\get(HTTPDownloader::class))
        ->method('addDownloader', DI\get(FTPDownloader::class))
        ->method('addDownloader', DI\get(LocalDownloader::class))
];
