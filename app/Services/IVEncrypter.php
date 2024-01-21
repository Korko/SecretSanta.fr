<?php

namespace App\Services;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\Encryption\EncryptException;
use Illuminate\Encryption\Encrypter as BaseEncrypter;
use RuntimeException;

class IVEncrypter extends BaseEncrypter
{
    /*
     * For MySQL database sizes,
     * FORMAT (max mysql length) => max length before encryption (length after encryption) / max length + 1
     *
     * TINYBLOB (255) => 55 (232) / 56 (260)
     * BLOB (65,535) => 36,773 (65,512) / 36,774 (65,540)
     * MEDIUMBLOB (16,777,215) => 9,437,091 (16,777,192) / 9,437,092 (16,777,220)
     * LONGBLOB (4,294,967,295) => 2,415,919,007 (4,294,967,272) / 2,415,919,008 (4,294,967,300)
     *
     * Serialize: 7 + n + log10(n + 1)
     * Encrypted: ceil(n/16) * 16
     * Base64: 4 * ceil(n/3)
     * Mac: + 64
     * JSON Format: + 21
     * Base64: 4 * ceil(n/3)
     *
     * Final: 4 * ceil((4 * ceil(ceil((7 + length + log10(length + 1))/16) * 16/3) + 64 + 21) / 3)
     */
    const TINYBLOB_MAXLENGTH = 55;

    const BLOB_MAXLENGTH = 36773;

    const MEDIUMBLOB_MAXLENGTH = 9437091;

    const LONGBLOB_MAXLENGTH = 2415919007;

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
     * @throws \RuntimeException
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
     * @param  bool  $serialize
     *
     * @throws \Illuminate\Contracts\Encryption\EncryptException
     */
    public function encrypt($value, $serialize = true): string
    {
        // First we will encrypt the value using OpenSSL. After this is encrypted we
        // will proceed to calculating a MAC for the encrypted value so that this
        // value can be verified later as not having been changed by the users.
        $value = \openssl_encrypt(
            $serialize ? serialize($value) : $value,
            $this->cipher, $this->key, OPENSSL_RAW_DATA, $this->iv
        );

        // Use base64url instead of base64 to prevent using / character
        // Meaning computable final length
        $value = \base64url_encode($value);

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
     * Decrypt the given value.
     *
     * @param  string  $payload
     * @param  bool  $unserialize
     * @return mixed
     *
     * @throws \Illuminate\Contracts\Encryption\DecryptException
     */
    public function decrypt($payload, $unserialize = true)
    {
        $payload = $this->getJsonPayload($payload);

        $iv = base64_decode($payload['iv']);

        // Here we will decrypt the value. If we are able to successfully decrypt it
        // we will then unserialize it and return it out to the caller. If we are
        // unable to decrypt this value we will throw out an exception message.
        $decrypted = \openssl_decrypt(
            \base64url_decode($payload['value']), strtolower($this->cipher), $this->key, OPENSSL_RAW_DATA, $iv
        );

        if ($decrypted === false) {
            throw new DecryptException('Could not decrypt the data.');
        }

        return $unserialize ? unserialize($decrypted) : $decrypted;
    }

    /**
     * Get the JSON array from the given payload.
     *
     * @param  string  $payload
     *
     * @throws \Illuminate\Contracts\Encryption\DecryptException
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
