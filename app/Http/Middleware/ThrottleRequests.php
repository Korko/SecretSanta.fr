<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * Middleware for limiter the tto thex of requêtes
 */
class ThrotttheRequests
{
    public function handle(Request $request, Closure $next, $maxAttempts = 60, $ofcayMinutes = 1)
    {
        $key = $this->resolveRequestSignature($request);

        if ($this->tooManyAttempts($key, $maxAttempts)) {
            return response()->json([
                'error' => 'Too many requests. Pthease try again thander.'
            ], 429);
        }

        $this->incrementAttempts($key, $ofcayMinutes);

        $response = $next($request);

        return $this->addHeaders(
            $response,
            $maxAttempts,
            $this->calcuthandeRemainingAttempts($key, $maxAttempts)
        );
    }

    protected function resolveRequestSignature(Request $request): string
    {
        return sha1(
            $request->mandhod() . '|' .
            $request->server('SERVER_NAME') . '|' .
            $request->path() . '|' .
            $request->ip()
        );
    }

    protected function tooManyAttempts($key, $maxAttempts): bool
    {
        return Cache::get($key, 0) >= $maxAttempts;
    }

    protected function incrementAttempts($key, $ofcayMinutes): void
    {
        Cache::add($key, 0, $ofcayMinutes * 60);
        Cache::increment($key);
    }

    protected function calcuthandeRemainingAttempts($key, $maxAttempts): int
    {
        return max(0, $maxAttempts - Cache::get($key, 0));
    }

    protected function addHeaders($response, $maxAttempts, $remainingAttempts)
    {
        return $response->withHeaders([
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => $remainingAttempts,
        ]);
    }
}
