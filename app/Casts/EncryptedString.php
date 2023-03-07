<?php

namespace App\Casts;

use App\Facades\DrawCrypt;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class EncryptedString implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @return mixed
     */
    public function get(Model $model, string $key, mixed $value, array $attributes)
    {
        return DrawCrypt::decrypt($value);
    }

    /**
     * Prepare the given value for storage.
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): string
    {
        return DrawCrypt::encrypt($value);
    }
}
