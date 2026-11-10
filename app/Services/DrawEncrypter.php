<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\Encryption\Encrypter as EncrypterContract;
use Illuminate\Contracts\Encryption\StringEncrypter;
use Illuminate\Encryption\Encrypter;
use RuntimeException;
use SensitiveParameter;

use function hash_hkdf;

/**
 * Encrypts the data of a draw with a key derived from both APP_KEY and the draw secret.
 *
 * The secret is shared by every link of the draw (organizer panel and participants)
 * and is never stored: without it, even with the database and APP_KEY, nothing can
 * be decrypted. Each value gets its own random IV (standard Laravel payload).
 *
 * Data written by the previous scheme (LegacyDrawEncrypter) is still readable and
 * gets rewritten in this format the next time it is saved.
 */
final class DrawEncrypter implements EncrypterContract, StringEncrypter
{
    /** Length of the draw secret, in bytes. Kept at 16 so existing links stay valid. */
    public const int SECRET_LENGTH = 16;

    private const string HKDF_INFO = 'secretsanta:draw:v2';

    private string $secret;

    private ?Encrypter $encrypter = null;

    private ?LegacyDrawEncrypter $legacy = null;

    /** @param list<string> $previousKeys */
    public function __construct(
        private readonly string $appKey,
        private readonly string $cipher = 'AES-256-CBC',
        private readonly array $previousKeys = [],
    ) {
        throw_unless(Encrypter::supported($appKey, $cipher), RuntimeException::class, 'Unsupported cipher or incorrect key length.');

        $this->secret = random_bytes(self::SECRET_LENGTH);
    }

    /**
     * Set the draw secret (taken from the link fragment).
     */
    public function setIV(string $secret): void
    {
        throw_unless(mb_strlen($secret, '8bit') === self::SECRET_LENGTH, RuntimeException::class, "The draw secret's length does not match the expected one.");

        $this->secret = $secret;
        $this->encrypter = null;
        $this->legacy = null;
    }

    /**
     * Get the draw secret (to put in the link fragment).
     */
    public function getIV(): string
    {
        return $this->secret;
    }

    public function encrypt(#[SensitiveParameter] $value, $serialize = true): string
    {
        return $this->encrypter()->encrypt($value, $serialize);
    }

    public function encryptString(#[SensitiveParameter] $value): string
    {
        return $this->encrypt($value, false);
    }

    public function decrypt($payload, $unserialize = true): mixed
    {
        if (self::isLegacyPayload($payload)) {
            return $this->legacy()->decrypt($payload, $unserialize);
        }

        return $this->encrypter()->decrypt($payload, $unserialize);
    }

    public function decryptString($payload): string
    {
        $value = $this->decrypt($payload, false);
        throw_unless(is_string($value), DecryptException::class, 'The payload is invalid.');

        return $value;
    }

    public function getKey(): string
    {
        return $this->encrypter()->getKey();
    }

    /** @return array<int, string> */
    public function getAllKeys(): array
    {
        return $this->encrypter()->getAllKeys();
    }

    /** @return array<int, string> */
    public function getPreviousKeys(): array
    {
        return $this->encrypter()->getPreviousKeys();
    }

    /**
     * Payloads of the previous scheme have no "iv" entry (the IV was the draw secret).
     */
    private static function isLegacyPayload(mixed $payload): bool
    {
        $decoded = is_string($payload) ? json_decode(base64_decode($payload, true) ?: '', true) : null;

        return is_array($decoded) && ! array_key_exists('iv', $decoded);
    }

    private function deriveKey(string $appKey): string
    {
        return hash_hkdf('sha256', $appKey, mb_strlen($appKey, '8bit'), self::HKDF_INFO, $this->secret);
    }

    private function encrypter(): Encrypter
    {
        return $this->encrypter ??= new Encrypter($this->deriveKey($this->appKey), $this->cipher)
            ->previousKeys(array_map($this->deriveKey(...), $this->previousKeys));
    }

    private function legacy(): LegacyDrawEncrypter
    {
        if (! $this->legacy instanceof LegacyDrawEncrypter) {
            $this->legacy = new LegacyDrawEncrypter($this->appKey, $this->cipher)->previousKeys($this->previousKeys);
            $this->legacy->setIV($this->secret);
        }

        return $this->legacy;
    }
}
