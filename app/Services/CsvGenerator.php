<?php

declare(strict_types=1);

namespace App\Services;

use RuntimeException;
use Stringable;

final class CsvGenerator implements Stringable
{
    public function __construct(
        /** @var array<int, array<int|string, bool|float|int|string|null>> */
        public array $data,
        public string $delimiter = ',',
        public string $enclosure = '"',
        public string $escapeChar = '\\'
    ) {}

    public function __toString(): string
    {
        $fileHandler = fopen('php://memory', 'r+') ?: throw new RuntimeException('Cannot open memory stream');
        foreach ($this->data as $fields) {
            if (fputcsv($fileHandler, $fields, $this->delimiter, $this->enclosure, $this->escapeChar) === false) {
                return '';
            }
        }

        rewind($fileHandler);
        $csvLine = stream_get_contents($fileHandler);

        return mb_rtrim((string) $csvLine);
    }

    /** @param array<int, array<int|string, bool|float|int|string|null>> $data */
    public function prepend(array $data): self
    {
        $this->data = array_merge(
            $data,
            $this->data
        );

        return $this;
    }

    /** @param array<int, array<int|string, bool|float|int|string|null>> $data */
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
