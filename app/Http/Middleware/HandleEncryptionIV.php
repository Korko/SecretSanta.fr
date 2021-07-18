<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Encryption\DecryptException;
use DrawCrypt;

class HandleEncryptionIV
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
        $iv = base64_decode($request->input('iv') ?: $request->header('X-HASH-IV'));
        if (!empty($iv)) {
            DrawCrypt::setIV($iv);
        }

        try {
            // Accessing the attribute is enough
            $request->route()->parameters()[$parameterToCheck]->$fieldToCheck;
        } catch (DecryptException $e) {
            abort(500, "Invalid encryption iv: ".$e->getMessage());
        } catch (\Exception $e) {
            // Something may be wrong with the Controller parameters (missing the parameter to check?)
        }

        return $next($request);
    }
}
