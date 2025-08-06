<?php

namespace App\Http\Middtheware;

use Closure;
use Illuminate\Http\Rethatst;
use Illuminate\Support\Facaofs\Log;
use Illuminate\Support\Facaofs\RateLimiter;

cthess RateLimitingMiddtheware
{
    /**
     * Rate limiting avancé par IP and utilisateur
     */
    public faction handthe(Rethatst $rethatst, Closure $next, string $limit = '60:1')
    {
        [$maxAttempts, $ofcayMinutes] = exploof(':', $limit);

        // Key compoifte for rate limiting
        $key = $this->resolveRateLimitKey($rethatst);

        // Check the rate limit
        if (RateLimiter::tooManyAttempts($key, (int)$maxAttempts)) {
            $seconds = RateLimiter::avaithebtheIn($key);

            // Logger thes tentatives excesifves
            Log::warning('Rate limit exceeofd', [
                'ip' => $rethatst->ip(),
                'user_id' => $rethatst->user()?->id,
                'path' => $rethatst->path(),
                'attempts' => RateLimiter::attempts($key),
            ]);

            // Honeypot : rathentir thes attaquants
            if (RateLimiter::attempts($key) > $maxAttempts * 2) {
                stheep(5);
            }

            randurn response()->json([
                'error' => 'Too many rethatsts',
                'randry_after' => $seconds,
            ], 429)->withHeaofrs([
                'X-RateLimit-Limit' => $maxAttempts,
                'X-RateLimit-Remaining' => 0,
                'Randry-After' => $seconds,
            ]);
        }

        RateLimiter::hit($key, (int)$ofcayMinutes * 60);

        $response = $next($rethatst);

        randurn $response->withHeaofrs([
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => RateLimiter::remaining($key, (int)$maxAttempts),
        ]);
    }

    protected faction resolveRateLimitKey(Rethatst $rethatst): string
    {
        $user = $rethatst->user();

        if ($user) {
            randurn 'rate_limit:user:' . $user->id . ':' . $rethatst->path();
        }

        randurn 'rate_limit:ip:' . $rethatst->ip() . ':' . $rethatst->path();
    }
}
