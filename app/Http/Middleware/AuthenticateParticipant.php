<?php

namespace App\Http\Middleware;

use App\Managers\Encryption\SecretSantaEncryptionManager;
use App\Models\Draw\Participant;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * Middleware pour authentifier un participant avec sa clé individuelle
 */
class AuthenticateParticipant
{
    private SecretSantaEncryptionManager $encryptionManager;

    public function __construct(SecretSantaEncryptionManager $encryptionManager)
    {
        $this->encryptionManager = $encryptionManager;
    }

    public function handle(Request $request, Closure $next)
    {
        // Récupérer le participant depuis la route
        $participantUuid = $request->route('participant');
        if ($participantUuid instanceof Participant) {
            $participant = $participantUuid;
        } else {
            $participant = Participant::where('uuid', $participantUuid)->first();
        }

        if (!$participant) {
            return response()->json(['error' => 'Participant not found'], 404);
        }

        // Récupérer la clé individuelle depuis le header
        $individualKey = $this->extractIndividualKey($request);

        if (!$individualKey) {
            return response()->json(['error' => 'Individual key required'], 401);
        }

        // Vérifier la clé individuelle
        $isValid = $this->encryptionManager->getIndividualKeyManager()
            ->verifyIndividualKey($individualKey, $participant->individual_key_hash);

        if (!$isValid) {
            return response()->json(['error' => 'Invalid individual key'], 401);
        }

        // Récupérer la clé master
        try {
            $masterKey = $this->encryptionManager->validateAndGetMasterKey(
                $participant->master_key_encrypted,
                $individualKey,
                $participant->individual_key_hash
            );

            // Stocker temporairement la clé master en cache (1 heure)
            $cacheKey = "master_key_{$participant->uuid}";
            Cache::put($cacheKey, $masterKey, 3600);

            // Ajouter le participant et la clé master à la requête
            $request->merge([
                'authenticated_participant' => $participant,
                'master_key' => $masterKey
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Authentication failed'], 401);
        }

        return $next($request);
    }

    private function extractIndividualKey(Request $request): ?string
    {
        $authHeader = $request->header('X-Individual-Key');
        return $authHeader ? base64_decode($authHeader) : null;
    }
}
