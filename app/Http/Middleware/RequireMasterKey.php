<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Middleware pour vérifier la clé master
 */
class RequireMasterKey
{
    public function handle(Request $request, Closure $next)
    {
        $masterKey = $this->extractMasterKey($request);

        if (!$masterKey) {
            return response()->json(['error' => 'Master key required'], 401);
        }

        // Ajouter la clé master à la requête
        $request->merge(['master_key' => $masterKey]);

        return $next($request);
    }

    private function extractMasterKey(Request $request): ?string
    {
        $authHeader = $request->header('X-Master-Key');
        return $authHeader ? base64_decode($authHeader) : null;
    }
}
