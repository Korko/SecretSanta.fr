<?php

declare(strict_types=1);

namespace App\Casts;

use DrawCrypt;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/** @implements CastsAttributes<string|null, string|null> */
final class EncryptedString implements CastsAttributes
{
    /**
     * Cast the given value.
     */
    /** @param array<string, mixed> $attributes */
    public function get(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        $decrypted = is_string($value) ? DrawCrypt::decrypt($value) : null;

        return is_scalar($decrypted) ? (string) $decrypted : null;
    }

    /**
     * Prepare the given value for storage.
     */
    /** @param array<string, mixed> $attributes */
    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        return is_null($value) ? null : DrawCrypt::encrypt($value);
    }
}
