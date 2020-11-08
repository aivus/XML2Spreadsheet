<?php

namespace aivus\XML2Spreadsheet\Converter;

use aivus\XML2Spreadsheet\Context;
use aivus\XML2Spreadsheet\Exception\InvalidAccessTokenException;
use aivus\XML2Spreadsheet\Google\Client;
use aivus\XML2Spreadsheet\Parser\ParserInterface;
use Psr\Log\LoggerInterface;

/**
 * Converts source file using specified parser to spreadsheet document
 */
class Converter
{
    private ?ParserInterface $currentParser = null;
    private Client $client;
    private LoggerInterface $logger;

    public function __construct(Client $client, LoggerInterface $logger)
    {
        $this->client = $client;
        $this->logger = $logger;
    }

    /**
     * Set current parser (Strategy)
     */
    public function setParser(ParserInterface $parser)
    {
        $this->logger->debug(
            'Setting parser {parserName} as a current for converter',
            ['parserName' => $parser::getName()]
        );

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

        $this->logger->info(
            'Start parsing source file using {parserName}',
            ['parserName' => $this->currentParser::getName()]
        );

        $spreadsheetData = $this->currentParser->parseResource($file);

        $this->logger->debug('Successfully parsed', ['headers' => $spreadsheetData->getHeaderNames()]);

        $this->applyContext($context);

        try {
            $spreadsheetInfo = $this->client->createSpreadsheet($spreadsheetData);
        } catch (\Exception $e) {
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
