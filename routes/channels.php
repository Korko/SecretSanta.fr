<?php

use App\Models\Draw\Participant;
use App\Models\Draw\Draw;
use Illuminate\Support\Facades\Broadcast;

// Public channel for draw updates
Broadcast::channel('draw.{drawUuid}', function ($user, $drawUuid) {
    // Everyone can listen to public updates of a draw
    return Draw::where('uuid', $drawUuid)->exists();
});

// Private channel for participants
Broadcast::channel('participant.{participantUuid}', function ($user, $participantUuid) {
    // Verify that user has access to this participant
    $participant = Participant::where('uuid', $participantUuid)->first();

    if (!$participant) {
        return false;
    }

    // Verify authentication via individual key
    $individualKey = request()->header('X-Individual-Key');
    if (!$individualKey) {
        return false;
    }

    $encryptionManager = app(\App\Services\Encryption\SecretSantaEncryptionManager::class);
    return $encryptionManager->getIndividualKeyManager()
        ->verifyIndividualKey(base64_decode($individualKey), $participant->individual_key_hash);
});

// Presence channel to see who is online
Broadcast::channel('draw.{drawUuid}.presence', function ($user, $drawUuid) {
    $draw = Draw::where('uuid', $drawUuid)->first();

    if (!$draw) {
        return false;
    }

    // Return presence info
    return [
        'id' => $user->id ?? uniqid(),
        'name' => $user->name ?? 'Anonymous',
    ];
});
