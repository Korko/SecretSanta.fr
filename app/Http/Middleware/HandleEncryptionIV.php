<?php

namespace App\Http\Middleware;

use App\Facades\DrawCrypt;
use Closure;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

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
            if(!is_object($request->route()->parameters()[$parameterToCheck])) {
                throw new \Exception('Parameter is not an object (substitution gone wrong?)');
            }

            $request->route()->parameters()[$parameterToCheck]->$fieldToCheck;
        } catch (DecryptException $e) {
            abort(500, 'Invalid encryption iv: '.$e->getMessage());
        } catch (\Exception $e) {
            dd($e);
            // Something may be wrong with the Controller parameters (missing the parameter to check?)
        }

        return $next($request);
    }
}
