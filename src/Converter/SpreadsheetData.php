<?php

namespace aivus\XML2Spreadsheet\Converter;

/**
 * Contains content of future spreadsheet document
 */
class SpreadsheetData
{
    private array $rows = [];
    private array $headerNames = [];

    public function addRow(array $item): void
    {
        $this->rows[] = array_values($item);
        $this->addUniqueHeaderName($item);
    }

    public function getRows(): array
    {
        return $this->rows;
    }

    /**
     * Returns header names based on the stored data
     */
    public function getHeaderNames(): array
    {
        return $this->headerNames;
    }

    private function addUniqueHeaderName(array $item): void
    {
        foreach ($item as $key => $value) {
            if (!in_array($key, $this->headerNames, true)) {
                $this->headerNames[] = $key;
            }
        }
    }
}
