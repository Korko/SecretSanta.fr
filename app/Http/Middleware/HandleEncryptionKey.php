<?php

namespace App\Http\Middleware;

use Closure;
use Crypt;

class HandleEncryptionKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $key = base64_decode($request->input('key'));
        Crypt::setKey($key);

        return $next($request);
    }
}
