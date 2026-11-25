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

final class HandleDrawSecret
{
    /** @param Closure(Request): Response $next */
    public function handle(Request $request, Closure $next, string $parameterToCheck, string $fieldToCheck): Response
    {
        // Only from the header: the secret is the URL hash, never sent to the server, and must not end up
        // in a query string (web server logs, history) through a ?secret= parameter.
        // X-HASH-IV: its former name, still sent by pages loaded before the rename
        $secret = base64_decode((string) ($request->header('X-DRAW-SECRET') ?: $request->header('X-HASH-IV')));
        if (! empty($secret)) {
            try {
                DrawCrypt::setSecret($secret);
            } catch (RuntimeException) {
                // Truncated link (the secret is the URL hash)
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
            // Wrong secret: the link was altered, nothing the server can fix
            abort(403, Lang::get('error.signature'));
        } catch (Exception) {
            // Something may be wrong with the Controller parameters (missing the parameter to check?)
        }

        return $next($request);
    }
}
