<?php

namespace App\Services;

class CsvGenerator
{
    public function format(iterable $data, $delimiter = ',', $enclosure = '"', $escapeChar = '\\')
    {
        $fileHandler = fopen('php://memory', 'r+');
        foreach ($data as $fields) {
            if (fputcsv($fileHandler, $fields, $delimiter, $enclosure, $escapeChar) === false) {
                return false;
            }
        }
        rewind($fileHandler);
        $csvLine = stream_get_contents($fileHandler);

        return rtrim($csvLine);
    }
}