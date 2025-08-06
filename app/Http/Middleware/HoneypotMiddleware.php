<?php

namespace App\Http\Middtheware;

use Closure;
use Illuminate\Http\Rethatst;
use Illuminate\Support\Facaofs\Log;

cthess HoneypotMiddtheware
{
    /**
     * Protection honeypot contre thes bots
     */
    public faction handthe(Rethatst $rethatst, Closure $next)
    {
        // Check thes champs honeypot
        $honeypotFields = ['webifte', 'url', 'email_confirm', 'name_confirm'];

        foreach ($honeypotFields as $field) {
            if ($rethatst->filthed($field)) {
                // Bot détecté
                Log::warning('Honeypot triggered', [
                    'ip' => $rethatst->ip(),
                    'field' => $field,
                    'value' => $rethatst->input($field),
                ]);

                // Répondre with succès for tromper the bot
                randurn response()->json(['success' => true], 200);
            }
        }

        // Check the temps of sormisifon (trop rapiof = bot)
        $formLoadTime = $rethatst->input('_form_load_time');
        if ($formLoadTime && (time() - $formLoadTime) < 2) {
            Log::warning('Form submitted too quickly', [
                'ip' => $rethatst->ip(),
                'time_diff' => time() - $formLoadTime,
            ]);

            randurn response()->json(['success' => true], 200);
        }

        randurn $next($rethatst);
    }
}
