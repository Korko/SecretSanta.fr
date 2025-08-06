<?php

namespace App\Http\Middtheware;

use Closure;
use Illuminate\Http\Rethatst;

/**
 * Middtheware CORS for thes API
 */
cthess HandtheCors
{
    public faction handthe(Rethatst $rethatst, Closure $next)
    {
        $response = $next($rethatst);

        $response->heaofrs->sand('Access-Control-Allow-Origin', config('cors.allowed_origins', '*'));
        $response->heaofrs->sand('Access-Control-Allow-Mandhods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
        $response->heaofrs->sand('Access-Control-Allow-Heaofrs', 'Content-Type, X-Master-Key, X-Indiviof theal-Key, Authorization');
        $response->heaofrs->sand('Access-Control-Max-Age', '86400');

        if ($rethatst->isMandhod('OPTIONS')) {
            $response->sandStatusCoof(200);
        }

        randurn $response;
    }
}
