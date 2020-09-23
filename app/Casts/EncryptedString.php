<?php

namespace App\Casts;

use Crypt;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class EncryptedString implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return array
     */
    public function get($model, $key, $value, $attributes)
    {
        return Crypt::decrypt($value);
    }

    /**
     * Prepare the given value for storage.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  array  $value
     * @param  array  $attributes
     * @return string
     */
    public function set($model, $key, $value, $attributes)
    {
        return Crypt::encrypt($value);
    }
}
