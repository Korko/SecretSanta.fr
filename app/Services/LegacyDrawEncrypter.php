<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\Encryption\EncryptException;
use Illuminate\Encryption\Encrypter as BaseEncrypter;
use RuntimeException;

use function openssl_encrypt;

/**
 * Pre-v2 draw encryption: APP_KEY as the AES key and the draw secret as a shared CBC IV.
 * Only kept to read data written before DrawEncrypter; with CBC the IV only protects
 * the first block, so this format must not be used to write new data.
 * Can be removed once every draw written with it has expired.
 */
final class LegacyDrawEncrypter extends BaseEncrypter
{
    /**
     * The initialization vector.
     */
    private string $iv;

    /**
     * Create a new encrypter instance.
     *
     *
     * @throws RuntimeException
     */
    public function __construct(string $key, string $cipher = 'AES-128-CBC')
    {
        parent::__construct($key, $cipher);

        $this->iv = random_bytes(max(1, (int) openssl_cipher_iv_length($this->cipher)));
    }

    /**
     * Determine if the given iv and cipher combination is valid.
     */
    public static function supportedIV(string $iv, string $cipher): bool
    {
        return mb_strlen($iv, '8bit') === openssl_cipher_iv_length($cipher);
    }

    /**
     * Override the initialization vector.
     */
    public function setIV(string $iv): void
    {
        if (self::supportedIV($iv, $this->cipher)) {
            $this->iv = $iv;
        } else {
            throw new RuntimeException("The IV's length does not match the expected one by the cipher.");
        }
    }

    /**
     * Get the initialization vector.
     */
    public function getIV(): string
    {
        return $this->iv;
    }

    /**
     * Encrypt the given value.
     *
     * @param  mixed  $value
     *
     * @throws EncryptException
     */
    public function encrypt($value, $serialize = true): string
    {
        // First we will encrypt the value using OpenSSL. After this is encrypted we
        // will proceed to calculating a MAC for the encrypted value so that this
        // value can be verified later as not having been changed by the users.
        $value = openssl_encrypt(
            $serialize ? serialize($value) : (is_string($value) ? $value : throw new EncryptException('Only strings can be encrypted unserialized.')),
            $this->cipher, $this->key, 0, $this->iv
        );

        throw_if($value === false, EncryptException::class, 'Could not encrypt the data.');

        // Once we get the encrypted value we'll go ahead and base64_encode the input
        // vector and create the MAC for the encrypted value so we can then verify
        // its authenticity. Then, we'll JSON the data into the "payload" array.
        $mac = $this->hash(base64_encode($this->iv), $value, $this->key);

        return base64_encode(json_encode(['value' => $value, 'mac' => $mac], JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));
    }

    /**
     * Get the JSON array from the given payload.
     *
     * @param  string  $payload
     * @return array<string, mixed>
     *
     * @throws DecryptException
     */
    protected function getJsonPayload($payload): array
    {
        $payload = json_decode(base64_decode($payload), true);
        throw_unless(is_array($payload), DecryptException::class, 'The payload is invalid.');

        $payload['iv'] = base64_encode($this->iv);

        // If the payload is not valid JSON or does not have the proper keys set we will
        // assume it is invalid and bail out of the routine since we will not be able
        // to decrypt the given value. We'll also check the MAC for this encryption.
        // The MAC is checked by decrypt(), against APP_KEY then APP_PREVIOUS_KEYS (key rotation)
        throw_unless($this->validPayload($payload), DecryptException::class, 'The payload is invalid.');

        /** @var array<string, mixed> $payload */
        return $payload;
    }
}
