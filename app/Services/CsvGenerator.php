<?php

declare(strict_types=1);

namespace App\Services;

use Stringable;

final class CsvGenerator implements Stringable
{
    public function __construct(
        /**
         * @var mixed[]
         */
        public array $data,
        public $delimiter = ',',
        public $enclosure = '"',
        public $escapeChar = '\\'
    ) {}

    public function __toString(): string
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

    public function prepend(array $data): self
    {
        $this->data = array_merge(
            $data,
            $this->data
        );

        return $this;
    }

    public function append(array $data): self
    {
        $this->data = array_merge(
            $this->data,
            $data
        );

        return $this;
    }

    public function format(): string
    {
        return (string) $this;
    }
}
