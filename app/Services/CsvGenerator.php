<?php

namespace App\Services;

class CsvGenerator
{
    public function __construct(
        public array $data,
        public string $delimiter = ',',
        public string $enclosure = '"',
        public string $escapeChar = '\\'
    ) {
    }

    public function prepend(array $data)
    {
        $this->data = array_merge(
            $data,
            $this->data
        );

        return $this;
    }

    public function append(array $data)
    {
        $this->data = array_merge(
            $this->data,
            $data
        );

        return $this;
    }

    public function format()
    {
        return (string) $this;
    }

    public function __toString()
    {
        $fileHandler = fopen('php://memory', 'r+');
        foreach ($this->data as $fields) {
            if (fputcsv($fileHandler, $fields, $this->delimiter, $this->enclosure, $this->escapeChar) === false) {
                return '';
            }
        }
        rewind($fileHandler);
        $csvLine = stream_get_contents($fileHandler);

        return rtrim($csvLine);
    }
}
