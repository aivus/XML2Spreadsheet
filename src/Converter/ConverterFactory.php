<?php

namespace aivus\XML2Spreadsheet\Converter;

use aivus\XML2Spreadsheet\Google\Client;
use Psr\Log\LoggerInterface;

/**
 * Factory for Converter class
 * New converter should be used each time because it uses Strategy pattern to set parser
 */
class ConverterFactory
{
    private Client $client;
    private LoggerInterface $logger;

    public function __construct(Client $client, LoggerInterface $logger)
    {
        $this->client = $client;
        $this->logger = $logger;
    }

    public function create(): Converter
    {
        return new Converter($this->client, $this->logger);
    }
}
