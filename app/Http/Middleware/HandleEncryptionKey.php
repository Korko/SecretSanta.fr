<?php

namespace App\Http\Middleware;

use Closure;
use Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class HandleEncryptionKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $parameterToCheck, $fieldToCheck)
    {
        $key = base64_decode($request->input('key'));
        if (is_string($key)) {
            Crypt::setKey($key);
        }

        try {
            // Accessing the attribute is enough
            $request->route()->parameters()[$parameterToCheck]->$fieldToCheck;
        } catch (DecryptException $e) {
            abort(500, "Invalid encryption key");
        }

        return $next($request);
    }
}
