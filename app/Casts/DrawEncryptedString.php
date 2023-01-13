<?php

namespace App\Casts;

use App\Facades\DrawCrypt;
use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class DrawEncryptedString implements Castable
{
    /**
     * Get the caster class to use when casting from / to this cast target.
     *
     * @param  array  $arguments
     * @return object|string
     */
    public static function castUsing(array $arguments)
    {
        return new class implements CastsAttributes
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
            public function get($model, $key, $value, $attributes)
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
            public function set($model, $key, $value, $attributes)
            {
                return DrawCrypt::encrypt($value);
            }
        };
    }
}
