<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class SecurityHeaders
{
    /** @param Closure(Request): Response $next */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Page URLs hold the signature and the draw/participant hash: never sent to another site
        $response->headers->set('Referrer-Policy', 'same-origin', false);
        // The organizer panel (reveal, delete...) can't be framed by another page to trick clicks
        $response->headers->set('X-Frame-Options', 'DENY', false);
        $response->headers->set('Content-Security-Policy', "frame-ancestors 'none'", false);
        $response->headers->set('X-Content-Type-Options', 'nosniff', false);

        return $response;
    }
}
