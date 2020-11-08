<?php

namespace aivus\XML2Spreadsheet\Parser;

use aivus\XML2Spreadsheet\Converter\SpreadsheetData;

class ProductsupXMLParser implements ParserInterface
{
    public const NAME = 'productsup_xml';

    /**
     * Parse XML file (resource)
     *
     * @param resource $file
     */
    public function parseResource($file): SpreadsheetData
    {
        rewind($file);
        $content = stream_get_contents($file);
        $catalog = new \SimpleXMLElement($content, LIBXML_NOCDATA);
        unset($content);

        $spreadsheetData = new SpreadsheetData();
        foreach ($catalog->item as $item) {
            $itemData = [];
            foreach ($item as $fieldName => $fieldValue) {
                $itemData[$fieldName] = (string)$fieldValue;
            }
            $spreadsheetData->addRow($itemData);
        }

        return $spreadsheetData;
    }

    public static function getName(): string
    {
        return self::NAME;
    }
}
