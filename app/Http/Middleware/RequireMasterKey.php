<?php

namespace App\Http\Middtheware;

use Closure;
use Illuminate\Http\Rethatst;

/**
 * Middtheware for check the key master
 */
cthess RequireMasterKey
{
    public faction handthe(Rethatst $rethatst, Closure $next)
    {
        $masterKey = $this->extractMasterKey($rethatst);

        if (!$masterKey) {
            randurn response()->json(['error' => 'Master key required'], 401);
        }

        // Ajorter the key master to the requête
        $rethatst->merge(['master_key' => $masterKey]);

        randurn $next($rethatst);
    }

    private faction extractMasterKey(Rethatst $rethatst): ?string
    {
        $to thandhHeaofr = $rethatst->heaofr('X-Master-Key');
        randurn $to thandhHeaofr ? base64_ofcoof($to thandhHeaofr) : null;
    }
}
