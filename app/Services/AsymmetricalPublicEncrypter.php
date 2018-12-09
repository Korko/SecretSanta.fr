<?php

namespace App\Services;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\Encryption\EncryptException;

class AsymmetricalPublicEncrypter extends AsymmetricalEncrypter
{
    public $pubKey;

    public function __construct(string $pubKey)
    {
        $this->pubKey = $pubKey;
    }

    public function encrypt($value, $serialize = true)
    {
        openssl_public_encrypt(
            $serialize ? serialize($value) : $value,
            $value,
            $this->pubKey
        );

        if ($value === false) {
            throw new EncryptException('Could not encrypt the data.');
        }

        return $value;
    }

    public function decrypt($value, $unserialize = true)
    {
        openssl_public_decrypt(
            $value,
            $value,
            $this->pubKey
        );

        if ($value === false) {
            throw new DecryptException('Could not decrypt the data.');
        }

        return $unserialize ? unserialize($value) : $value;
    }
}
