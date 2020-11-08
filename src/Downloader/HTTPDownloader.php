<?php

namespace aivus\XML2Spreadsheet\Downloader;

use aivus\XML2Spreadsheet\Context;
use GuzzleHttp\Client;

/**
 * Downloader for remote web files which can be loaded by http/https protocols
 */
class HTTPDownloader implements DownloaderInterface
{
    private Client $guzzleClient;

    public function __construct(Client $guzzleClient)
    {
        $this->guzzleClient = $guzzleClient;
    }

    public function getFileByURI(string $uri, Context $context)
    {
        $method = $context->getOption('httpMethod', 'GET');
        // TODO: Add ability to use headers(e.g. cookies from the context)

        $file = tmpfile();
        $this->guzzleClient->request($method, $uri, ['sink' => $file]);

        return $file;
    }

    public function isSchemaSupported($schema): bool
    {
        return in_array(strtolower($schema), ['http', 'https']);
    }
}
