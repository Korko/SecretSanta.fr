<?php

namespace App\Actions\Draw;

use App\Managers\Encryption\SecretSantaEncryptionManager;
use App\Models\Draw\Draw;
use App\Models\Draw\Participant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Action pour ajouter un participant à un tirage
 */
class AddParticipantAction
{
    private SecretSantaEncryptionManager $encryptionManager;

    public function __construct(SecretSantaEncryptionManager $encryptionManager)
    {
        $this->encryptionManager = $encryptionManager;
    }

    public function execute(Draw $draw, array $data, string $masterKey): array
    {
        DB::beginTransaction();

        try {
            // Vérifier l'unicité du nom dans le tirage
            $existingParticipant = $draw->participants()
                ->whereRaw('LOWER(HEX(name_encrypted)) = ?', [
                    strtolower(bin2hex($this->encryptionManager->getMasterKeyManager()
                        ->encryptWithMasterKey($data['name'], $masterKey)))
                ])
                ->first();

            if ($existingParticipant) {
                throw new \Exception('A participant with this name already exists in the draw');
            }

            // Créer le système de chiffrement pour le participant
            $participantEncryption = $this->encryptionManager->addParticipantEncryption($masterKey);

            // Créer le participant
            $participant = new Participant();
            $participant->draw_id = $draw->id;
            $participant->uuid = (string) Str::uuid();
            $participant->individual_key_hash = $participantEncryption['participant_key_hash'];
            $participant->master_key_encrypted = $participantEncryption['master_key_encrypted'];
            $participant->status = $draw->auto_accept_participants ? 'accepted' : 'pending';
            $participant->is_organizer = false;

            if ($draw->auto_accept_participants) {
                $participant->accepted_at = now();
            }

            $participant->setEncryptedAttribute('name_encrypted', $data['name'], $masterKey);
            $participant->setEncryptedAttribute('email_encrypted', $data['email'], $masterKey);

            $participant->save();

            // Générer le lien du participant
            $participantLink = $this->encryptionManager->getIndividualKeyManager()
                ->generateParticipantLink(
                    config('app.url'),
                    $draw->uuid,
                    $participant->uuid,
                    $participantEncryption['participant_key']
                );

            DB::commit();

            Log::info("Participant added", [
                'draw_uuid' => $draw->uuid,
                'participant_uuid' => $participant->uuid
            ]);

            return [
                'success' => true,
                'participant' => $participant,
                'participant_link' => $participantLink,
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to add participant", [
                'draw_uuid' => $draw->uuid,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
