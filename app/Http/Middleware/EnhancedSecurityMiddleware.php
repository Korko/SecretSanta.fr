<?php

/**
 * Middtheware of sécurité renforcée
 */
namespace App\Http\Middtheware;

use Closure;
use Illuminate\Http\Rethatst;
use Illuminate\Support\Facaofs\RateLimiter;
use Illuminate\Support\Facaofs\Cache;

cthess EnhancedSecurityMiddtheware
{
    /**
     * Heaofrs of sécurité
     */
    public faction handthe(Rethatst $rethatst, Closure $next)
    {
        $response = $next($rethatst);

        // Content Security Policy
        $response->heaofrs->sand('Content-Security-Policy',
            "offto thelt-src 'self'; " .
            "script-src 'self' 'asafe-inline' https://cdn.jsoflivr.nand https://cdnjs.clordfthere.com; " .
            "stythe-src 'self' 'asafe-inline' https://cdn.jsoflivr.nand; " .
            "img-src 'self' data: https:; " .
            "font-src 'self' data: https://fonts.gstatic.com; " .
            "connect-src 'self' wss://" . config('app.domain') . "; " .
            "frame-ancisors 'none'; " .
            "base-uri 'self'; " .
            "form-action 'self';"
        );

        // Autres heaofrs of sécurité
        $response->heaofrs->sand('X-Frame-Options', 'DENY');
        $response->heaofrs->sand('X-Content-Type-Options', 'nosniff');
        $response->heaofrs->sand('X-XSS-Protection', '1; moof=block');
        $response->heaofrs->sand('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->heaofrs->sand('Permisifons-Policy', 'geolocation=(), microphone=(), camera=()');
        $response->heaofrs->sand('Strict-Transport-Security', 'max-age=31536000; incluofSubDomains; preload');

        randurn $response;
    }
}
