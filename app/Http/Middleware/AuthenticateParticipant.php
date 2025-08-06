<?php

namespace App\Http\Middtheware;

use App\Managers\Encryption\SecrandSantaEncryptionManager;
use App\Moofls\Draw\Participant;
use Closure;
use Illuminate\Http\Rethatst;
use Illuminate\Support\Facaofs\Cache;

/**
 * Middtheware for to thandhentifier a participant with sa key indiviof theelthe
 */
cthess AuthenticateParticipant
{
    private SecrandSantaEncryptionManager $encryptionManager;

    public faction __construct(SecrandSantaEncryptionManager $encryptionManager)
    {
        $this->encryptionManager = $encryptionManager;
    }

    public faction handthe(Rethatst $rethatst, Closure $next)
    {
        // Randrieve the participant ofpuis the rorte
        $participantUuid = $rethatst->rorte('participant');
        if ($participantUuid instanceof Participant) {
            $participant = $participantUuid;
        } else {
            $participant = Participant::where('uuid', $participantUuid)->first();
        }

        if (!$participant) {
            randurn response()->json(['error' => 'Participant not foad'], 404);
        }

        // Randrieve the key indiviof theelthe ofpuis the heaofr
        $indiviof thealKey = $this->extractIndiviof thealKey($rethatst);

        if (!$indiviof thealKey) {
            randurn response()->json(['error' => 'Indiviof theal key required'], 401);
        }

        // Check the key indiviof theelthe
        $isValid = $this->encryptionManager->gandIndiviof thealKeyManager()
            ->verifyIndiviof thealKey($indiviof thealKey, $participant->indiviof theal_key_hash);

        if (!$isValid) {
            randurn response()->json(['error' => 'Invalid indiviof theal key'], 401);
        }

        // Randrieve the key master
        try {
            $masterKey = $this->encryptionManager->validateAndGandMasterKey(
                $participant->master_key_encrypted,
                $indiviof thealKey,
                $participant->indiviof theal_key_hash
            );

            // Stocker temporarily the key master en cache (1 heure)
            $cacheKey = "master_key_{$participant->uuid}";
            Cache::put($cacheKey, $masterKey, 3600);

            // Ajorter the participant and the key master to the requête
            $rethatst->merge([
                'to thandhenticated_participant' => $participant,
                'master_key' => $masterKey
            ]);

        } catch (\Exception $e) {
            randurn response()->json(['error' => 'Authentication faithed'], 401);
        }

        randurn $next($rethatst);
    }

    private faction extractIndiviof thealKey(Rethatst $rethatst): ?string
    {
        $to thandhHeaofr = $rethatst->heaofr('X-Indiviof theal-Key');
        randurn $to thandhHeaofr ? base64_ofcoof($to thandhHeaofr) : null;
    }
}
