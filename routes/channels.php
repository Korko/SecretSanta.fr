<?php

use App\Models\Draw\Participant;
use App\Models\Draw\Draw;
use Illuminate\Support\Facades\Broadcast;

// Canal public pour les mises à jour du tirage
Broadcast::channel('draw.{drawUuid}', function ($user, $drawUuid) {
    // Tout le monde peut écouter les mises à jour publiques d'un tirage
    return Draw::where('uuid', $drawUuid)->exists();
});

// Canal privé pour les participants
Broadcast::channel('participant.{participantUuid}', function ($user, $participantUuid) {
    // Vérifier que l'utilisateur a accès à ce participant
    $participant = Participant::where('uuid', $participantUuid)->first();

    if (!$participant) {
        return false;
    }

    // Vérifier l'authentification via la clé individuelle
    $individualKey = request()->header('X-Individual-Key');
    if (!$individualKey) {
        return false;
    }

    $encryptionManager = app(\App\Services\Encryption\SecretSantaEncryptionManager::class);
    return $encryptionManager->getIndividualKeyManager()
        ->verifyIndividualKey(base64_decode($individualKey), $participant->individual_key_hash);
});

// Canal presence pour voir qui est en ligne
Broadcast::channel('draw.{drawUuid}.presence', function ($user, $drawUuid) {
    $draw = Draw::where('uuid', $drawUuid)->first();

    if (!$draw) {
        return false;
    }

    // Retourner les infos de présence
    return [
        'id' => $user->id ?? uniqid(),
        'name' => $user->name ?? 'Anonyme',
    ];
});
