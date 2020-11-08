<?php

use aivus\XML2Spreadsheet\Downloader\{DownloaderRegistry, FTPDownloader, HTTPDownloader, LocalDownloader};
use aivus\XML2Spreadsheet\Parser\ProductsupXMLParser;

return [
    'parser.' . ProductsupXMLParser::getName() => DI\create(ProductsupXMLParser::class),
    DownloaderRegistry::class => DI\create()
        ->method('addDownloader', DI\get(HTTPDownloader::class))
        ->method('addDownloader', DI\get(FTPDownloader::class))
        ->method('addDownloader', DI\get(LocalDownloader::class))
];
