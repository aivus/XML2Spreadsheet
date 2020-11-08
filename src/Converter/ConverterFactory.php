<?php

namespace aivus\XML2Spreadsheet\Converter;

use aivus\XML2Spreadsheet\Google\Client;

/**
 * Factory for Converter class
 * New converter should be used each time because it uses Strategy pattern to set parser
 */
class ConverterFactory
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function create(): Converter
    {
        return new Converter($this->client);
    }
}
