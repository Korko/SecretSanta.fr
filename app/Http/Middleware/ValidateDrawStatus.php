<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Middleware pour valider le statut d'un tirage
 */
class ValidateDrawStatus
{
    public function handle(Request $request, Closure $next, ...$allowedStatuses)
    {
        $draw = $request->route('draw');

        if (!$draw) {
            return response()->json(['error' => 'Draw not found'], 404);
        }

        if (!in_array($draw->status, $allowedStatuses)) {
            return response()->json([
                'error' => "Draw must be in one of the following states: " . implode(', ', $allowedStatuses),
                'current_status' => $draw->status
            ], 422);
        }

        return $next($request);
    }
}
