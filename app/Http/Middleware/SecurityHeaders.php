<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Symfony\Component\HttpFoundation\Response;

final class SecurityHeaders
{
    /** @param Closure(Request): Response $next */
    public function handle(Request $request, Closure $next): Response
    {
        // Before rendering: the @vite tags and the @javascript block get this nonce
        $nonce = Vite::useCspNonce();

        $response = $next($request);

        // Page URLs hold the signature and the draw/participant hash: never sent to another site
        $response->headers->set('Referrer-Policy', 'same-origin', false);
        // The organizer panel (reveal, delete...) can't be framed by another page to trick clicks
        $response->headers->set('X-Frame-Options', 'DENY', false);
        $response->headers->set('Content-Security-Policy', $this->contentSecurityPolicy($nonce), false);
        $response->headers->set('X-Content-Type-Options', 'nosniff', false);

        return $response;
    }

    /**
     * The organizer and dearSanta pages hold the draw secret in location.hash: only the site's own
     * scripts may run, no third-party or injected one, and no eval: the Vue templates are all compiled at build time.
     */
    private function contentSecurityPolicy(string $nonce): string
    {
        $scripts = ["'self'", "'nonce-{$nonce}'"];
        if (Vite::isRunningHot()) {
            $scripts[] = mb_trim((string) file_get_contents(Vite::hotFile()));
        }

        return implode('; ', [
            'script-src '.implode(' ', $scripts),
            "object-src 'none'",
            "base-uri 'self'",
            "frame-ancestors 'none'",
        ]);
    }
}
