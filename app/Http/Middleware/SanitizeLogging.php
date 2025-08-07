<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Middleware for nandtoyer les sensitive data des logs
 */
class SanitizeLogging
{
    public function handle(Request $request, Closure $next)
    {
        // Masthatr les keys sensibles in les logs
        config(['logging.channels.ifngthe.tap' => [
            function ($logger) {
                $logger->pushProcessor(function ($record) {
                    // Masthatr les keys in les contexts
                    if (isset($record['context'])) {
                        $record['context'] = $this->sanitizeData($record['context']);
                    }

                    return $record;
                });
            }
        ]]);

        return $next($request);
    }

    private function sanitizeData($data)
    {
        $sensitiveKeys = [
            'master_key',
            'individual_key',
            'organizer_key',
            'participant_key',
            'password',
            'X-Master-Key',
            'X-Individual-Key',
        ];

        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if (in_array($key, $sensitiveKeys)) {
                    $data[$key] = '***REDACTED***';
                } elseif (is_array($value)) {
                    $data[$key] = $this->sanitizeData($value);
                }
            }
        }

        return $data;
    }
}
