<?php

namespace aivus\XML2Spreadsheet\Google;

use aivus\XML2Spreadsheet\Converter\SpreadsheetData;
use aivus\XML2Spreadsheet\Converter\SpreadsheetDocumentInfo;
use aivus\XML2Spreadsheet\Exception\InvalidConfigurationException;
use aivus\XML2Spreadsheet\Google\DTO\AccessTokenHolder;

class Client
{
    /**
     * @var string Default path to Google API credentials
     */
    private string $credentialsFilePath = 'config/credentials.json';

    /**
     * An instance of native Google Client.
     * Use getClient() method always instead of the property as it can be null
     *
     * @var \Google\Client|null
     */
    private ?\Google\Client $nativeClient = null;

    private Spreadsheet $spreadsheet;

    public function __construct(Spreadsheet $spreadsheet)
    {
        $this->spreadsheet = $spreadsheet;
    }

    /**
     * Authorize an access to the user's spreadsheet documents
     */
    public function getAuthUrl(): string
    {
        $client = $this->getNativeClient();

        return $client->createAuthUrl();
    }

    /**
     * Exchange authCode for accessToken and set it to the client
     */
    public function setAccessTokenByAuthCode(string $authCode): void
    {
        $client = $this->getNativeClient();
        $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
        if (array_key_exists('error', $accessToken)) {
            // TODO: Add logging
            throw new \InvalidArgumentException('Can not retrieve access token based on provided verification code');
        }

        $client->setAccessToken($accessToken);
    }

    /**
     * Set string representation of access token to native client
     */
    public function setAccessToken(string $accessToken): void
    {
        $this->getNativeClient()->setAccessToken($accessToken);
    }

    public function createSpreadsheet(SpreadsheetData $spreadsheetData): SpreadsheetDocumentInfo
    {
        $client = $this->getNativeClient();
        $spreadsheetDocument = $this->spreadsheet->createDocument($client);

        $values = $spreadsheetData->getRows();
        // Push header row (column names) on the first place
        array_unshift($values, $spreadsheetData->getHeaderNames());
        $this->spreadsheet->updateDocument($client, $spreadsheetDocument, $values);

        return new SpreadsheetDocumentInfo(
            $spreadsheetDocument->getSpreadsheetId(),
            $spreadsheetDocument->getSpreadsheetUrl()
        );
    }

    public function getAccessTokenHolder(): AccessTokenHolder
    {
        return AccessTokenHolder::create($this->getNativeClient()->getAccessToken());
    }

    /**
     * Set path to the file with credentials
     */
    public function setCredentialsFilePath(string $path): void
    {
        $this->credentialsFilePath = $path;
    }

    private function getNativeClient(): \Google\Client
    {
        if (!$this->nativeClient) {
            $this->ensureCredentialsFileReadable();
            $this->nativeClient = new \Google\Client();
            $this->nativeClient->setScopes(\Google_Service_Sheets::SPREADSHEETS);
            $this->nativeClient->setAuthConfig($this->credentialsFilePath);
        }

        return $this->nativeClient;
    }

    private function ensureCredentialsFileReadable()
    {
        if (!is_readable($this->credentialsFilePath)) {
            throw new InvalidConfigurationException(
                sprintf('Credentials file "%s" can not be read', $this->credentialsFilePath)
            );
        }
    }
}
