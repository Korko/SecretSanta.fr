<?php

namespace App\Services;

use Illuminate\Encryption\Encrypter;

class SymmetricalEncrypter extends Encrypter
{
    public function __construct(string $key)
    {
        parent::__construct($key, config('app.cipher'));
    }

    public function split($payload)
    {
        return $this->getJsonPayload($payload);
    }
}
