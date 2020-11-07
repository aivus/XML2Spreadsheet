<?php

namespace aivus\XML2Spreadsheet\Downloader;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class HTTPDownloader implements DownloaderInterface
{
    private Client $guzzleClient;

    public function __construct(Client $guzzleClient)
    {
        $this->guzzleClient = $guzzleClient;
    }

    /**
     * @inheritDoc
     */
    public function getFileByURI(string $uri)
    {
        $file = tmpfile();
        $this->guzzleClient->get($uri, ['sink' => $file]);

        return $file;
    }

    /**
     * @inheritDoc
     */
    public function isSchemaSupported(string $schema): bool
    {
        return in_array(strtolower($schema), ['http', 'https']);
    }
}
