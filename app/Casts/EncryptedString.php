<?php

namespace App\Casts;

use DrawCrypt;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

final class EncryptedString implements CastsAttributes
{
    /**
     * Cast the given value.
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return is_null($value) ? null : DrawCrypt::decrypt($value);
    }

    /**
     * Prepare the given value for storage.
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        return is_null($value) ? null : DrawCrypt::encrypt($value);
    }
}
