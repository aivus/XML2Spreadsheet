<?php

namespace aivus\XML2Spreadsheet\Google;

class Spreadsheet
{
    /**
     * Create new spreadsheet document
     */
    public function createDocument(\Google\Client $client): \Google_Service_Sheets_Spreadsheet
    {
        $service = $this->getSheetService($client);

        return $service->spreadsheets->create(new \Google_Service_Sheets_Spreadsheet());
    }

    /**
     * Update spreadsheet document with new data starting from the specified range
     */
    public function updateDocument(
        \Google\Client $client,
        \Google_Service_Sheets_Spreadsheet $spreadsheet,
        array $data,
        string $range = 'A1'
    ): \Google_Service_Sheets_UpdateValuesResponse {
        $service = $this->getSheetService($client);
        $body = new \Google_Service_Sheets_ValueRange([
            'values' => $data,
        ]);

        return $service->spreadsheets_values->update($spreadsheet->getSpreadsheetId(), $range, $body, [
            'valueInputOption' => 'RAW',
        ]);
    }

    private function getSheetService(\Google\Client $client): \Google_Service_Sheets
    {
        return new \Google_Service_Sheets($client);
    }
}
