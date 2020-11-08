<?php

use aivus\XML2Spreadsheet\Downloader\{DownloaderRegistry, FTPDownloader, HTTPDownloader, LocalDownloader};
use aivus\XML2Spreadsheet\Parser\ProductsupXMLParser;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;

return [
    'parser.' . ProductsupXMLParser::getName() => DI\create(ProductsupXMLParser::class),

    DownloaderRegistry::class => DI\autowire()
        ->method('addDownloader', DI\get(HTTPDownloader::class))
        ->method('addDownloader', DI\get(FTPDownloader::class))
        ->method('addDownloader', DI\get(LocalDownloader::class)),

    Psr\Log\LoggerInterface::class => DI\factory(function () {
        $logger = new Logger('default');
        $logger->pushProcessor(new PsrLogMessageProcessor());

        $fileHandler = new StreamHandler('var/logs/app.log', Logger::DEBUG);
        $fileHandler->setFormatter(new LineFormatter());
        $logger->pushHandler($fileHandler);

        return $logger;
    }),
];
