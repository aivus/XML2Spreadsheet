<?php

namespace aivus\XML2Spreadsheet\Downloader;

use aivus\XML2Spreadsheet\Context;
use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;

/**
 * Downloader for remote web files which can be loaded by http/https protocols
 */
class HTTPDownloader implements DownloaderInterface
{
    private Client $guzzleClient;
    private LoggerInterface $logger;

    public function __construct(Client $guzzleClient, LoggerInterface $logger)
    {
        $this->guzzleClient = $guzzleClient;
        $this->logger = $logger;
    }

    public function getFileByURI(string $uri, Context $context)
    {
        $method = $context->getOption('httpMethod', 'GET');
        // TODO: Add ability to use headers(e.g. cookies from the context)

        $this->logger->debug(
            'Downloading file from {uri} using {method} method',
            ['uri' => $uri, 'method' => $method]
        );

        $file = tmpfile();
        $result = $this->guzzleClient->request($method, $uri, ['sink' => $file]);
        $result->getBody()->detach();

        return $file;
    }

    public function isSchemaSupported($schema): bool
    {
        return in_array(strtolower($schema), ['http', 'https']);
    }
}
