<?php

namespace App\Services;

use Illuminate\Encryption\Encrypter;

class SymmetricalEncrypter extends Encrypter
{
    public function __construct(string $key)
    {
        parent::__construct($key, config('app.cipher));
    }

    public static function generateKey()
    {
        return parent::generateKey(config('app.cipher'));
    }

    public function splitPayload($payload)
    {
        return $this->getJsonPayload($payload);
    }
}

