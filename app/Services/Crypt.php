<?php

namespace App\Services;

class Crypt
{
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

    public function crypt($value, $key = null, $iv = null)
    {
        $cipher = config('app.cipher');

        $key = $key ?: $this->generateKey();
        $iv = $iv ?: $this->generateIv();

        return [
            bin2hex($iv).openssl_encrypt(serialize($value), $cipher, $key, 0, $iv),
            $key,
        ];
    }

    public function decrypt($value, $key)
    {
        $cipher = config('app.cipher');
        list($iv, $value) = $this->splitIv($value);

        $iv = hex2bin($iv);
        $value = openssl_decrypt($value, $cipher, $key, 0, $iv);

        return unserialize($value);
    }

    public function splitIv($value)
    {
        // Times 2 because we are in UTF8 and the iv is in hexadecimal
        $cipher = config('app.cipher');
        $ivLength = openssl_cipher_iv_length($cipher) * 2;

        return [substr($value, 0, $ivLength), substr($value, $ivLength)];
    }
}
