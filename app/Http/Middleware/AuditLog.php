<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Middleware pour journaliser les activités sensibles
 */
class AuditLog
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Journaliser les actions sensibles
        $sensitiveActions = [
            'POST /api/v1/draws/*/launch',
            'POST /api/v1/draws/*/reveal',
            'DELETE /api/v1/messages/*',
            'POST /api/v1/participants/*/regenerate-link',
        ];

        $currentPath = $request->method() . ' ' . $request->path();

        foreach ($sensitiveActions as $pattern) {
            if (fnmatch($pattern, $currentPath)) {
                \Log::channel('audit')->info('Sensitive action performed', [
                    'action' => $currentPath,
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'participant' => $request->get('authenticated_participant')?->uuid,
                    'response_status' => $response->getStatusCode(),
                ]);
                break;
            }
        }

        return $response;
    }
}
