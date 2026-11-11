<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use DrawCrypt;
use Exception;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class HandleEncryptionIV
{
    /** @param Closure(Request): Response $next */
    public function handle(Request $request, Closure $next, string $parameterToCheck, string $fieldToCheck): Response
    {
        // Only from the header: the secret is the URL hash, never sent to the server, and must not end up
        // in a query string (web server logs, history) through an ?iv= parameter
        $iv = base64_decode((string) $request->header('X-HASH-IV'));
        if (! empty($iv)) {
            DrawCrypt::setIV($iv);
        }

        try {
            // Accessing the attribute is enough
            $model = $request->route($parameterToCheck);
            if ($model instanceof Model) {
                $model->getAttribute($fieldToCheck);
            }
        } catch (DecryptException $e) {
            abort(500, 'Invalid encryption iv: '.$e->getMessage());
        } catch (Exception) {
            // Something may be wrong with the Controller parameters (missing the parameter to check?)
        }

        return $next($request);
    }
}
