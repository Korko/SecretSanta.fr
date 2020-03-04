<?php

namespace App\Http\Middleware;

class DecryptInput extends TransformsRequest
{
    /**
     * Transform the given key.
     *
     * @param  string $key
     * @return mixed
     */
    protected function transformKey($key)
    {
        return Crypt::decrypt($key);
    }
}
