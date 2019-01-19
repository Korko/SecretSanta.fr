<?php

namespace App\Services;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\Encryption\EncryptException;

class AsymmetricalPrivateEncrypter extends AsymmetricalEncrypter
{
    public $privKey;

    public function __construct(string $privKey)
    {
        $this->privKey = $privKey;
    }

    public function encrypt($value, $serialize = true)
    {
        openssl_private_encrypt(
            $serialize ? serialize($value) : $value,
            $value,
            $this->privKey
        );

        if ($value === false) {
            throw new EncryptException('Could not encrypt the data.');
        }

        return $value;
    }

    public function decrypt($value, $unserialize = true)
    {
        openssl_private_decrypt($value, $value, $this->privKey);

        if ($value === false) {
            throw new DecryptException('Could not decrypt the data.');
        }

        return $unserialize ? unserialize($value) : $value;
    }
}
