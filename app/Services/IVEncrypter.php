<?php

namespace App\Services;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\Encryption\EncryptException;
use Illuminate\Encryption\Encrypter as BaseEncrypter;
use RuntimeException;

use function openssl_encrypt;

class IVEncrypter extends BaseEncrypter
{
    /**
     * The initialization vector.
     *
     * @var string
     */
    protected $iv;

    /**
     * Create a new encrypter instance.
     *
     * @return void
     *
     * @throws RuntimeException
     */
    public function __construct(string $key, string $cipher = 'AES-128-CBC')
    {
        parent::__construct($key, $cipher);

        $this->iv = random_bytes(openssl_cipher_iv_length($this->cipher));
    }

    /**
     * Override the initialization vector.
     */
    public function setIV(string $iv): void
    {
        if (static::supportedIV($iv, $this->cipher)) {
            $this->iv = $iv;
        } else {
            throw new RuntimeException('The IV\'s length does not match the expected one by the cipher.');
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
     * Determine if the given iv and cipher combination is valid.
     */
    public static function supportedIV(string $iv, string $cipher): bool
    {
        return mb_strlen($iv, '8bit') === openssl_cipher_iv_length($cipher);
    }

    /**
     * Encrypt the given value.
     *
     * @param  mixed  $value
     * @param  bool   $serialize
     *
     * @throws EncryptException
     */
    public function encrypt($value, $serialize = true): string
    {
        // First we will encrypt the value using OpenSSL. After this is encrypted we
        // will proceed to calculating a MAC for the encrypted value so that this
        // value can be verified later as not having been changed by the users.
        $value = openssl_encrypt(
            $serialize ? serialize($value) : $value,
            $this->cipher, $this->key, 0, $this->iv
        );

        if ($value === false) {
            throw new EncryptException('Could not encrypt the data.');
        }

        // Once we get the encrypted value we'll go ahead and base64_encode the input
        // vector and create the MAC for the encrypted value so we can then verify
        // its authenticity. Then, we'll JSON the data into the "payload" array.
        $mac = $this->hash(base64_encode($this->iv), $value);

        $json = json_encode(compact('value', 'mac'), JSON_UNESCAPED_SLASHES);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new EncryptException('Could not encrypt the data.');
        }

        return base64_encode($json);
    }

    /**
     * Get the JSON array from the given payload.
     *
     * @param string $payload
     *
     * @throws DecryptException
     */
    protected function getJsonPayload($payload): array
    {
        $payload = json_decode(base64_decode($payload), true);

        $payload['iv'] = base64_encode($this->iv);

        // If the payload is not valid JSON or does not have the proper keys set we will
        // assume it is invalid and bail out of the routine since we will not be able
        // to decrypt the given value. We'll also check the MAC for this encryption.
        if (! $this->validPayload($payload)) {
            throw new DecryptException('The payload is invalid.');
        }

        if (! $this->validMac($payload)) {
            throw new DecryptException('The MAC is invalid.');
        }

        return $payload;
    }
}
