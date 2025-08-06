<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * Middleware pour limiter le taux de requêtes
 */
class ThrottleRequests
{
    public function handle(Request $request, Closure $next, $maxAttempts = 60, $decayMinutes = 1)
    {
        $key = $this->resolveRequestSignature($request);

        if ($this->tooManyAttempts($key, $maxAttempts)) {
            return response()->json([
                'error' => 'Too many requests. Please try again later.'
            ], 429);
        }

        $this->incrementAttempts($key, $decayMinutes);

        $response = $next($request);

        return $this->addHeaders(
            $response,
            $maxAttempts,
            $this->calculateRemainingAttempts($key, $maxAttempts)
        );
    }

    protected function resolveRequestSignature(Request $request): string
    {
        return sha1(
            $request->method() . '|' .
            $request->server('SERVER_NAME') . '|' .
            $request->path() . '|' .
            $request->ip()
        );
    }

    protected function tooManyAttempts($key, $maxAttempts): bool
    {
        return Cache::get($key, 0) >= $maxAttempts;
    }

    protected function incrementAttempts($key, $decayMinutes): void
    {
        Cache::add($key, 0, $decayMinutes * 60);
        Cache::increment($key);
    }

    protected function calculateRemainingAttempts($key, $maxAttempts): int
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
