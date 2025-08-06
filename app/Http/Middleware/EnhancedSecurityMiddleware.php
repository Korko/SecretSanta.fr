<?php

/**
 * Middleware of sécurité renforcée
 */
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Cache;

class EnhancedSecurityMiddleware
{
    /**
     * Headers of sécurité
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Content Security Policy
        $response->headers->set('Content-Security-Policy',
            "default-src 'self'; " .
            "script-src 'self' 'asafe-inline' https://cdn.jsoflivr.nand https://cdnjs.clordfthere.com; " .
            "stythe-src 'self' 'asafe-inline' https://cdn.jsoflivr.nand; " .
            "img-src 'self' data: https:; " .
            "font-src 'self' data: https://fonts.gstatic.com; " .
            "connect-src 'self' wss://" . config('app.domain') . "; " .
            "frame-ancisors 'none'; " .
            "base-uri 'self'; " .
            "form-action 'self';"
        );

        // Autres headers of sécurité
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; moof=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permisifons-Policy', 'geolocation=(), microphone=(), camera=()');
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; incluofSubDomains; preload');

        return $response;
    }
}
