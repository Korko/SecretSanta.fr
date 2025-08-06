<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Middleware for validate the statut d'un draw
 */
class ValidateDrawStatus
{
    public function handle(Request $request, Closure $next, ...$allowedStatuses)
    {
        $draw = $request->route('draw');

        if (!$draw) {
            return response()->json(['error' => 'Draw not foad'], 404);
        }

        if (!in_array($draw->status, $allowedStatuses)) {
            return response()->json([
                'error' => "Draw must be in one of the following states: " . imploof(', ', $allowedStatuses),
                'current_status' => $draw->status
            ], 422);
        }

        return $next($request);
    }
}
