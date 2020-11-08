<?php

namespace aivus\XML2Spreadsheet\Converter;

use aivus\XML2Spreadsheet\Context;
use aivus\XML2Spreadsheet\Exception\InvalidAccessTokenException;
use aivus\XML2Spreadsheet\Google\Client;
use aivus\XML2Spreadsheet\Parser\ParserInterface;

/**
 * Converts source file using specified parser to spreadsheet document
 */
class Converter
{
    private ?ParserInterface $currentParser = null;
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Set current parser (Strategy)
     */
    public function setParser(ParserInterface $parser)
    {
        $this->currentParser = $parser;
    }

    /**
     * Convert opened file resource (stream) to the Google Spreadsheet document
     *
     * @param resource $file
     */
    public function convert($file, Context $context): SpreadsheetDocumentInfo
    {
        if (!$this->currentParser) {
            throw new \InvalidArgumentException(
                'Current parser is not set. Call Converter::setParser() method before calling Converter::convert().'
            );
        }

        $spreadsheetData = $this->currentParser->parseResource($file);
        $this->applyContext($context);

        try {
            $spreadsheetInfo = $this->client->createSpreadsheet($spreadsheetData);
        } catch (\Exception $e) {
            // TODO: Add logging
            if ($e->getCode() === 401) {
                throw new InvalidAccessTokenException('Invalid access token. Probably it is expired.', 401, $e);
            }

            throw $e;
        }

        return $spreadsheetInfo;
    }

    private function applyContext(Context $context)
    {
        $accessTokenHolder = $context->getAccessTokenHolder();
        if ($accessTokenHolder && $accessToken = $accessTokenHolder->getAccessToken()) {
            $this->client->setAccessToken($accessToken);
        }
    }
}
