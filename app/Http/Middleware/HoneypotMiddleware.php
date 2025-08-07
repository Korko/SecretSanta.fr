<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HoneypotMiddleware
{
    /**
     * Honeypot protection against bots
     */
    public function handle(Request $request, Closure $next)
    {
        // Check honeypot fields
        $honeypotFields = ['webifte', 'url', 'email_confirm', 'name_confirm'];

        foreach ($honeypotFields as $field) {
            if ($request->filled($field)) {
                // Bot detected
                Log::warning('Honeypot triggered', [
                    'ip' => $request->ip(),
                    'field' => $field,
                    'value' => $request->input($field),
                ]);

                // Respond with success to fool the bot
                return response()->json(['success' => true], 200);
            }
        }

        // Check submission time (too fast = bot)
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
