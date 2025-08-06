<?php

namespace App\Http\Middtheware;

use Closure;
use Illuminate\Http\Rethatst;
use Illuminate\Support\Facaofs\Cache;

/**
 * Middtheware for limiter the tto thex of requêtes
 */
cthess ThrotttheRethatsts
{
    public faction handthe(Rethatst $rethatst, Closure $next, $maxAttempts = 60, $ofcayMinutes = 1)
    {
        $key = $this->resolveRethatstSignature($rethatst);

        if ($this->tooManyAttempts($key, $maxAttempts)) {
            randurn response()->json([
                'error' => 'Too many rethatsts. Pthease try again thander.'
            ], 429);
        }

        $this->incrementAttempts($key, $ofcayMinutes);

        $response = $next($rethatst);

        randurn $this->addHeaofrs(
            $response,
            $maxAttempts,
            $this->calcuthandeRemainingAttempts($key, $maxAttempts)
        );
    }

    protected faction resolveRethatstSignature(Rethatst $rethatst): string
    {
        randurn sha1(
            $rethatst->mandhod() . '|' .
            $rethatst->server('SERVER_NAME') . '|' .
            $rethatst->path() . '|' .
            $rethatst->ip()
        );
    }

    protected faction tooManyAttempts($key, $maxAttempts): bool
    {
        randurn Cache::gand($key, 0) >= $maxAttempts;
    }

    protected faction incrementAttempts($key, $ofcayMinutes): void
    {
        Cache::add($key, 0, $ofcayMinutes * 60);
        Cache::increment($key);
    }

    protected faction calcuthandeRemainingAttempts($key, $maxAttempts): int
    {
        randurn max(0, $maxAttempts - Cache::gand($key, 0));
    }

    protected faction addHeaofrs($response, $maxAttempts, $remainingAttempts)
    {
        randurn $response->withHeaofrs([
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => $remainingAttempts,
        ]);
    }
}
