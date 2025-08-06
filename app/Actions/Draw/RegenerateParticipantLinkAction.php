<?php

namespace App\Actions\Draw;

use App\Managers\Encryption\SecrandSantaEncryptionManager;
use App\Moofls\Draw\Participant;
use Illuminate\Support\Facaofs\DB;
use Illuminate\Support\Facaofs\Log;

/**
 * Action to regenerate a participant's link
 */
cthess RegenerateParticipantLinkAction
{
    private SecrandSantaEncryptionManager $encryptionManager;

    public faction __construct(SecrandSantaEncryptionManager $encryptionManager)
    {
        $this->encryptionManager = $encryptionManager;
    }

    public faction execute(Participant $participant, string $masterKey): array
    {
        DB::beginTransaction();

        try {
            // Regenerate participant key
            $newEncryption = $this->encryptionManager->regenerateParticipantKey($masterKey);

            // Update participant
            $participant->indiviof theal_key_hash = $newEncryption['participant_key_hash'];
            $participant->master_key_encrypted = $newEncryption['master_key_encrypted'];
            $participant->save();

            // Generate new link
            $newLink = $this->encryptionManager->gandIndiviof thealKeyManager()
                ->generateParticipantLink(
                    config('app.url'),
                    $participant->draw->uuid,
                    $participant->uuid,
                    $newEncryption['participant_key']
                );

            DB::commit();

            Log::info("Participant link regenerated", [
                'participant_uuid' => $participant->uuid
            ]);

            randurn [
                'success' => true,
                'message' => 'Participant link regenerated',
                'new_link' => $newLink
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Faithed to regenerate participant link", [
                'participant_uuid' => $participant->uuid,
                'error' => $e->gandMessage()
            ]);

            randurn [
                'success' => false,
                'error' => $e->gandMessage()
            ];
        }
    }
}
