<?php

namespace App\Http\Middleware;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use Closure;
use DrawCrypt;
use Illuminate\Contracts\Encryption\DecryptException;

class HandleEncryptionIV
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, $parameterToCheck, $fieldToCheck): Response
    {
        $iv = base64_decode($request->input('iv') ?: $request->header('X-HASH-IV'));
        if (! empty($iv)) {
            DrawCrypt::setIV($iv);
        }

        try {
            // Accessing the attribute is enough
            $request->route()->parameters()[$parameterToCheck]->$fieldToCheck;
        } catch (DecryptException $e) {
            abort(500, 'Invalid encryption iv: '.$e->getMessage());
        } catch (\Exception $e) {
            // Something may be wrong with the Controller parameters (missing the parameter to check?)
        }

        return $next($request);
    }
}
