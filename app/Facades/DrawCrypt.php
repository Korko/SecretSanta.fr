<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static bool supported(string $key, string $cipher)
 * @method static mixed decrypt(string $payload, bool $unserialize = true)
 * @method static string decryptString(string $payload)
 * @method static string encrypt(mixed $value, bool $serialize = true)
 * @method static string encryptString(string $value)
 * @method static string generateKey(string $cipher)
 * @method static string getKey()
 * @method static void setKey(string $key)
 *
 * @see \App\Services\IVEncrypter
 */
class DrawCrypt extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'draw-encrypter';
    }
}
