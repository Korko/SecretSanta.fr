<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Encryption\DecryptException;
use DrawCrypt;

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
        $key = base64_decode($request->input('key') ?: $request->header('X-HASH-KEY'));
        if (!empty($key)) {
            DrawCrypt::setKey($key);
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
