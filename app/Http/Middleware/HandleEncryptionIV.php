<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use DrawCrypt;
use Exception;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

final class HandleEncryptionIV
{
    /** @param Closure(Request): Response $next */
    public function handle(Request $request, Closure $next, string $parameterToCheck, string $fieldToCheck): Response
    {
        $iv = base64_decode($request->string('iv')->toString() ?: (string) $request->header('X-HASH-IV'));
        if (! empty($iv)) {
            try {
                DrawCrypt::setIV($iv);
            } catch (RuntimeException) {
                // Truncated link (the IV is the URL hash)
                abort(403, Lang::get('error.signature'));
            }
        }

        try {
            // Accessing the attribute is enough
            $model = $request->route($parameterToCheck);
            if ($model instanceof Model) {
                $model->getAttribute($fieldToCheck);
            }
        } catch (DecryptException) {
            // Wrong IV: the link was altered, nothing the server can fix
            abort(403, Lang::get('error.signature'));
        } catch (Exception) {
            // Something may be wrong with the Controller parameters (missing the parameter to check?)
        }

        return $next($request);
    }
}
