<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string format(iterable $data, $delimiter = ',', $enclosure = '"', $escapeChar = '\\')
 *
 * @see \App\Services\CsvGenerator
 */
class Csv extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'csv'; }
}