<?php

namespace App\Casts;

use App\Facades\DrawCrypt;
use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class DrawEncryptedString implements Castable
{
    /**
     * Get the caster class to use when casting from / to this cast target.
     *
     * @param  array<string, mixed>  $arguments
     */
    public static function castUsing(array $arguments): CastsAttributes
    {
        return new class implements CastsAttributes
        {
            public function get(Model $model, string $key, mixed $value, array $attributes): mixed
            {
                return DrawCrypt::decrypt($value);
            }

            public function set(Model $model, string $key, mixed $value, array $attributes): string
            {
                return DrawCrypt::encrypt($value);
            }
        };
    }
}
