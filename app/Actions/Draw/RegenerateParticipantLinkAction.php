<?php

namespace App\Actions\Draw;

use App\Managers\Encryption\SecretSantaEncryptionManager;
use App\Models\Draw\Participant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Action pour régénérer le lien d'un participant
 */
class RegenerateParticipantLinkAction
{
    private SecretSantaEncryptionManager $encryptionManager;

    public function __construct(SecretSantaEncryptionManager $encryptionManager)
    {
        $this->encryptionManager = $encryptionManager;
    }

    public function execute(Participant $participant, string $masterKey): array
    {
        DB::beginTransaction();

        try {
            // Régénérer la clé du participant
            $newEncryption = $this->encryptionManager->regenerateParticipantKey($masterKey);

            // Mettre à jour le participant
            $participant->individual_key_hash = $newEncryption['participant_key_hash'];
            $participant->master_key_encrypted = $newEncryption['master_key_encrypted'];
            $participant->save();

            // Générer le nouveau lien
            $newLink = $this->encryptionManager->getIndividualKeyManager()
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

            return [
                'success' => true,
                'message' => 'Participant link regenerated',
                'new_link' => $newLink
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to regenerate participant link", [
                'participant_uuid' => $participant->uuid,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
