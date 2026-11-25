<?php

declare(strict_types=1);

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static mixed decrypt(string $payload, bool $unserialize = true)
 * @method static string decryptString(string $payload)
 * @method static string encrypt(mixed $value, bool $serialize = true)
 * @method static string encryptString(string $value)
 * @method static string getKey()
 * @method static void setSecret(string $secret)
 * @method static string getSecret()
 * @method static string generateSecret()
 *
 * @see \App\Services\DrawEncrypter
 */
final class DrawCrypt extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'draw-encrypter';
    }
}
