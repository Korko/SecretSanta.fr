<?php

namespace App\Http\Middleware;

use Hashids;
use Illuminate\Foundation\Http\Middleware\TransformsRequest as Middleware;

class Unhash extends Middleware
{
    /**
     * Transform the given value.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return mixed
     */
    protected function transform($key, $value)
    {
        if (! array_key_exists($key, config('hashids.parameters'))) {
            return $value;
        }

        $connection = config('hashids.parameters')[$key];

        return array_get(Hashids::connection($connection)->decode($value), 0, $value);
    }
}
