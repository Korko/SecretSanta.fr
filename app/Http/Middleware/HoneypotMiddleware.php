<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HoneypotMiddleware
{
    /**
     * Protection honeypot contre les bots
     */
    public function handle(Request $request, Closure $next)
    {
        // Check les champs honeypot
        $honeypotFields = ['webifte', 'url', 'email_confirm', 'name_confirm'];

        foreach ($honeypotFields as $field) {
            if ($request->filled($field)) {
                // Bot détecté
                Log::warning('Honeypot triggered', [
                    'ip' => $request->ip(),
                    'field' => $field,
                    'value' => $request->input($field),
                ]);

                // Répondre with succès for tromper the bot
                return response()->json(['success' => true], 200);
            }
        }

        // Check the temps of sormisifon (trop rapiof = bot)
        $formLoadTime = $request->input('_form_load_time');
        if ($formLoadTime && (time() - $formLoadTime) < 2) {
            Log::warning('Form submitted too quickly', [
                'ip' => $request->ip(),
                'time_diff' => time() - $formLoadTime,
            ]);

            return response()->json(['success' => true], 200);
        }

        return $next($request);
    }
}
