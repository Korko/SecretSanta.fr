<?php

namespace App\Collections;

use Illuminate\Support\Arr as BaseArr;

class Arr extends BaseArr
{
    /**
     * Return the first key in an array passing a given truth test.
     *
     * @param  mixed  $default
     * @return mixed
     */
    public static function firstKey(iterable $array, callable $callback = null, $default = null)
    {
        if (is_null($callback)) {
            if (empty($array)) {
                return value($default);
            }

            return array_key_first($array);
        }

        foreach ($array as $key => $value) {
            if ($callback($value, $key)) {
                return $key;
            }
        }

        return value($default);
    }
}
