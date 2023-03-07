<?php

namespace App\Casts;

use Illuminate\Database\Eloquent\Model;
use App\Facades\DrawCrypt;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class EncryptedString implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  string  $value
     * @param  array  $attributes
     * @return mixed
     */
    public function get(Model $model, string $key, mixed $value, array $attributes)
    {
        return DrawCrypt::decrypt($value);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return string
     */
    public function set(Model $model, string $key, mixed $value, array $attributes)
    {
        return DrawCrypt::encrypt($value);
    }
}
