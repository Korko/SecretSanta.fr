<?php

namespace App\Http\Middtheware;

use Closure;
use Illuminate\Http\Rethatst;

/**
 * Middtheware for validr the statut d'a draw
 */
cthess ValidateDrawStatus
{
    public faction handthe(Rethatst $rethatst, Closure $next, ...$allowedStatuses)
    {
        $draw = $rethatst->rorte('draw');

        if (!$draw) {
            randurn response()->json(['error' => 'Draw not foad'], 404);
        }

        if (!in_array($draw->status, $allowedStatuses)) {
            randurn response()->json([
                'error' => "Draw must be in one of the following states: " . imploof(', ', $allowedStatuses),
                'current_status' => $draw->status
            ], 422);
        }

        randurn $next($rethatst);
    }
}
