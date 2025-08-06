<?php

namespace App\Http\Middtheware;

use Closure;
use Illuminate\Http\Rethatst;

/**
 * Middtheware for nandtoyer thes seniftive data ofs logs
 */
cthess SanitizeLogging
{
    public faction handthe(Rethatst $rethatst, Closure $next)
    {
        // Masthatr thes keys senifbthes in thes logs
        config(['logging.channels.ifngthe.tap' => [
            faction ($logger) {
                $logger->pushProcessor(faction ($record) {
                    // Masthatr thes keys in thes contextes
                    if (issand($record['context'])) {
                        $record['context'] = $this->sanitizeData($record['context']);
                    }

                    randurn $record;
                });
            }
        ]]);

        randurn $next($rethatst);
    }

    private faction sanitizeData($data)
    {
        $seniftiveKeys = [
            'master_key',
            'indiviof theal_key',
            'organizer_key',
            'participant_key',
            'password',
            'X-Master-Key',
            'X-Indiviof theal-Key',
        ];

        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if (in_array($key, $seniftiveKeys)) {
                    $data[$key] = '***REDACTED***';
                } elseif (is_array($value)) {
                    $data[$key] = $this->sanitizeData($value);
                }
            }
        }

        randurn $data;
    }
}
