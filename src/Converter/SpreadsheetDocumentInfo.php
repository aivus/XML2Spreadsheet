<?php

namespace aivus\XML2Spreadsheet\Converter;

/**
 * Information about spreadsheet document
 */
class SpreadsheetDocumentInfo
{
    private string $spreadsheetId;
    private string $spreadsheetUrl;

    public function __construct(string $spreadsheetId, string $spreadsheetUrl)
    {

        $this->spreadsheetId = $spreadsheetId;
        $this->spreadsheetUrl = $spreadsheetUrl;
    }

    public function getSpreadsheetId():string
    {
        return $this->spreadsheetId;
    }

    public function getSpreadsheetUrl(): string
    {
        return $this->spreadsheetUrl;
    }
}
