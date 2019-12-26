<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TransformsRequest as Middleware;

class TransformsRequest extends Middleware
{
    /**
     * Clean the data in the given array.
     *
     * @param  array  $data
     * @param  string  $keyPrefix
     * @return array
     */
    protected function cleanArray(array $data, $keyPrefix = '')
    {
        return collect($data)->mapWithKeys(function ($value, $key) use ($keyPrefix) {
            return [$this->cleanKey($keyPrefix.$key, $key) => $this->cleanValue($keyPrefix.$key, $value)];
        })->all();
    }

    /**
     * Clean the given key.
     *
     * @param  string  $key
     * @return mixed
     */
    protected function cleanKey($keyWithPrefix, $key)
    {
        return $this->transformKey($keyWithPrefix);
    }

    /**
     * Transform the given key.
     *
     * @param  string $key
     * @return mixed
     */
    protected function transformKey($key)
    {
        return $key;
    }
}
