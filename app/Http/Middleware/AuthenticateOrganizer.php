<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Middleware for check qu'a participant is organizer
 */
class AuthenticateOrganizer
{
    public function handle(Request $request, Closure $next)
    {
        $participant = $request->get('authenticated_participant');

        if (!$participant) {
            return response()->json(['error' => 'Authentication required'], 401);
        }

        if (!$participant->is_organizer) {
            return response()->json(['error' => 'Organizer access required'], 403);
        }

        return $next($request);
    }
}
