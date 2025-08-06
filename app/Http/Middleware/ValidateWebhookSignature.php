<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Middleware pour valider la signature des webhooks
 */
class ValidateWebhookSignature
{
    public function handle(Request $request, Closure $next)
    {
        $signature = $request->header('X-Webhook-Signature');

        if (!$signature) {
            return response()->json(['error' => 'Missing webhook signature'], 401);
        }

        $payload = $request->getContent();
        $secret = config('services.webhook.secret');
        $expectedSignature = hash_hmac('sha256', $payload, $secret);

        if (!hash_equals($expectedSignature, $signature)) {
            return response()->json(['error' => 'Invalid webhook signature'], 401);
        }

        return $next($request);
    }
}
