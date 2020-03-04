<?php

namespace App\Services;

use Illuminate\Encryption\Encrypter as BaseEncrypter;

class Encrypter extends BaseEncrypter
{
    public function setKey(string $key)
    {
        $this->__construct($key, $this->cipher);
    }
}
