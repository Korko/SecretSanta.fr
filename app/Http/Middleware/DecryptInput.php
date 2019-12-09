<?php

namespace App\Http\Middleware;

use Korko\Encrypter\SymmetricalEncrypter as Encrypter;

class DecryptInput extends TransformsRequest
{
    protected $decrypter;

    public function __construct()
    {
        parent::__construct();

        $key = md5(csrf_token().config('app.key'));
        $this->decrypter = new Encrypter($key);
    }

    /**
     * Transform the given key.
     *
     * @param  string $key
     * @return mixed
     */
    protected function transformKey($key)
    {
        return $this->decrypter->decrypt($key);
    }
}
