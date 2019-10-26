<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\TransformsRequest;

class DecryptInput extends TransformsRequest
{
    protected $decrypter;

    public function __construct()
    {
        parent::__construct();

        $key = md5(csrf_token() . config('app.key'));
        $this->decrypter = new Encrypter($key);
    }

    /**
     * Clean the given key.
     *
     * @param  string  $key
     * @return mixed
     */
    protected function cleanKey($key)
    {
        return $this->decrypter->decrypt($key);
    }
}
