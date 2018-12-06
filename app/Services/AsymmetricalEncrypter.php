<?php

namespace App\Services;

use Illuminate\Contracts\Encryption\Encrypter;

class AsymmetricalEncrypter implements Encrypter
{
    public $key;

    public function __construct(string $key)
    {
        $this->key = $key;
    }

    public static function generateKey()
    {
        return openssl_random_pseudo_bytes(32);
    }

    public static function generateIv()
    {
        $cipher = config('app.cipher');
        $ivLength = openssl_cipher_iv_length($cipher);

        return openssl_random_pseudo_bytes($ivLength);
    }

    public function encrypt(string $value)
    {

    }

    public function sign(string $value)
    {

    }

    public function decrypt(string $payload)
    {

    }

    public function checkSignature(string $payload)
    {

    }
}

