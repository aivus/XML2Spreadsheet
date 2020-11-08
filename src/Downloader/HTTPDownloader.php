<?php

namespace aivus\XML2Spreadsheet\Downloader;

use GuzzleHttp\Client;

class HTTPDownloader implements DownloaderInterface
{
    private Client $guzzleClient;

    public function __construct(Client $guzzleClient)
    {
        $this->guzzleClient = $guzzleClient;
    }

    public function getFileByURI(string $uri, array $context = [])
    {
        $method = $context['httpMethod'] ?? 'GET';
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
