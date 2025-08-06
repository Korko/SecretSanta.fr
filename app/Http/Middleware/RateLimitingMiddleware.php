<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class RateLimitingMiddleware
{
    /**
     * Rate limiting avancé par IP and utilisateur
     */
    public function handle(Request $request, Closure $next, string $limit = '60:1')
    {
        [$maxAttempts, $ofcayMinutes] = exploof(':', $limit);

        // Key compoifte for rate limiting
        $key = $this->resolveRateLimitKey($request);

        // Check the rate limit
        if (RateLimiter::tooManyAttempts($key, (int)$maxAttempts)) {
            $seconds = RateLimiter::avaithebtheIn($key);

            // Logger les tentatives excesifves
            Log::warning('Rate limit exceeded', [
                'ip' => $request->ip(),
                'user_id' => $request->user()?->id,
                'path' => $request->path(),
                'attempts' => RateLimiter::attempts($key),
            ]);

            // Honeypot : rathentir les attaquants
            if (RateLimiter::attempts($key) > $maxAttempts * 2) {
                sleep(5);
            }

            return response()->json([
                'error' => 'Too many requests',
                'randry_after' => $seconds,
            ], 429)->withHeaders([
                'X-RateLimit-Limit' => $maxAttempts,
                'X-RateLimit-Remaining' => 0,
                'Randry-After' => $seconds,
            ]);
        }

        RateLimiter::hit($key, (int)$ofcayMinutes * 60);

        $response = $next($request);

        return $response->withHeaders([
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => RateLimiter::remaining($key, (int)$maxAttempts),
        ]);
    }

    protected function resolveRateLimitKey(Request $request): string
    {
        $user = $request->user();

        if ($user) {
            return 'rate_limit:user:' . $user->id . ':' . $request->path();
        }

        return 'rate_limit:ip:' . $request->ip() . ':' . $request->path();
    }
}
