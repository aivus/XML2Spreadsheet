<?php

namespace aivus\XML2Spreadsheet\Parser;

use aivus\XML2Spreadsheet\Converter\SpreadsheetData;

interface ParserInterface
{
    /**
     * Parse a file (resource) to the internal spreadsheet representation
     *
     * @param resource $file
     */
    public function parseResource($file): SpreadsheetData;

    /**
     * Returns unique parser name which will be used to specify parser
     */
    public static function getName(): string;
}
