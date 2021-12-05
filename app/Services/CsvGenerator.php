<?php

namespace App\Services;

class CsvGenerator
{
    public $data;
    public $delimiter;
    public $enclosure;
    public $escapeChar;

    public function __construct(array $data, $delimiter = ',', $enclosure = '"', $escapeChar = '\\')
    {
        $this->data = $data;
        $this->delimiter = $delimiter;
        $this->enclosure = $enclosure;
        $this->escapeChar = $escapeChar;
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