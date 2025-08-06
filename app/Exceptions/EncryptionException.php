<?php

namespace App\Exceptions;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Exceptions personnalisées pour le système de chiffrement
 */
class EncryptionException extends \Exception
{
    public static function keyGenerationFailed(): self
    {
        return new self('Failed to generate encryption key');
    }

    public static function encryptionFailed(string $reason = ''): self
    {
        return new self('Encryption failed' . ($reason ? ": {$reason}" : ''));
    }

    public static function decryptionFailed(string $reason = ''): self
    {
        return new self('Decryption failed' . ($reason ? ": {$reason}" : ''));
    }

    public static function invalidKey(): self
    {
        return new self('Invalid encryption key provided');
    }

    public static function invalidData(): self
    {
        return new self('Invalid encrypted data format');
    }
}
