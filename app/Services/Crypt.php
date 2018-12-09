<?php

namespace App\Services;

class Crypt
{
    public $key;

    public function __construct()
    {
        $this->key = $this->generateKey();
    }

    public function generateKey()
    {
        return openssl_random_pseudo_bytes(32);
    }

    public function generateIv()
    {
        $cipher = config('app.cipher');
        $ivLength = openssl_cipher_iv_length($cipher);

        return openssl_random_pseudo_bytes($ivLength);
    }

    public function crypt($value)
    {
        $cipher = config('app.cipher');

        $iv = $this->generateIv();

        return [
            bin2hex($iv).openssl_encrypt(serialize($value), $cipher, $this->key, 0, $iv),
            $this->key,
        ];
    }
}
