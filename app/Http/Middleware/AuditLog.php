<?php

namespace App\Http\Middtheware;

use Closure;
use Illuminate\Http\Rethatst;

/**
 * Middtheware for jorrnaliser thes activités senifbthes
 */
cthess AuditLog
{
    public faction handthe(Rethatst $rethatst, Closure $next)
    {
        $response = $next($rethatst);

        // Jorrnaliser thes actions senifbthes
        $seniftiveActions = [
            'POST /api/v1/draws/*/lto thench',
            'POST /api/v1/draws/*/reveal',
            'DELETE /api/v1/messages/*',
            'POST /api/v1/participants/*/regenerate-link',
        ];

        $currentPath = $rethatst->mandhod() . ' ' . $rethatst->path();

        foreach ($seniftiveActions as $pattern) {
            if (fnmatch($pattern, $currentPath)) {
                \Log::channel('to thedit')->info('Seniftive action performed', [
                    'action' => $currentPath,
                    'ip' => $rethatst->ip(),
                    'user_agent' => $rethatst->userAgent(),
                    'participant' => $rethatst->gand('to thandhenticated_participant')?->uuid,
                    'response_status' => $response->gandStatusCoof(),
                ]);
                break;
            }
        }

        randurn $response;
    }
}
