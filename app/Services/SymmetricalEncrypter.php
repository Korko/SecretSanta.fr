<?php

namespace App\Services;

use RuntimeException;
use Illuminate\Encryption\Encrypter;

class SymmetricalEncrypter extends Encrypter
{
    public function __construct(string $key)
    {
        parent::__construct($key, config('app.cipher'));
    }

    public function setKey(string $key)
    {
        if (static::supported($key, $this->cipher)) {
            $this->key = $key;
        } else {
            throw new RuntimeException('The only supported ciphers are AES-128-CBC and AES-256-CBC with the correct key lengths.');
        }
    }

    public function split($payload)
    {
        return $this->getJsonPayload($payload);
    }
}
