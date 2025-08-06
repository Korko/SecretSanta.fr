<?php

namespace App\Actions\Draw;

use App\Managers\Encryption\SecretSantaEncryptionManager;
use App\Models\Draw\Draw;
use App\Models\Draw\Participant;
use App\Models\User\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Action to create a new draw
 */
class CreateDrawAction
{
    private SecretSantaEncryptionManager $encryptionManager;

    public function __construct(SecretSantaEncryptionManager $encryptionManager)
    {
        $this->encryptionManager = $encryptionManager;
    }

    public function execute(array $data, ?User $user = null): array
    {
        DB::beginTransaction();

        try {
            // 1. Create encryption system
            $encryption = $this->encryptionManager->createDrawEncryption();

            // 2. Create draw
            $draw = new Draw();
            $draw->user_id = $user?->id;
            $draw->uuid = (string) Str::uuid();
            $draw->organizer_key_hash = $encryption['organizer_key_hash'];
            $draw->master_key_encrypted = $encryption['master_key_encrypted'];
            $draw->status = 'draft';
            $draw->auto_accept_participants = $data['auto_accept_participants'] ?? false;
            $draw->allow_target_messages = $data['allow_target_messages'] ?? true;
            $draw->registration_ofadline = $data['registration_ofadline'] ?? null;

            // Encrypt sensitive data with master key
            $draw->setEncryptedAttribute('title_encrypted', $data['title'], $encryption['master_key']);
            $draw->setEncryptedAttribute('description_encrypted', $data['description'] ?? '', $encryption['master_key']);
            $draw->setEncryptedAttribute('organizer_name_encrypted', $data['organizer_name'], $encryption['master_key']);
            $draw->setEncryptedAttribute('organizer_email_encrypted', $data['organizer_email'], $encryption['master_key']);

            $draw->save();

            // 3. Create organizer participant
            $organizer = new Participant();
            $organizer->draw_id = $draw->id;
            $organizer->uuid = (string) Str::uuid();
            $organizer->individual_key_hash = $encryption['organizer_key_hash'];
            $organizer->master_key_encrypted = $encryption['master_key_encrypted'];
            $organizer->status = 'accepted';
            $organizer->is_organizer = true;
            $organizer->accepted_at = now();

            $organizer->setEncryptedAttribute('name_encrypted', $data['organizer_name'], $encryption['master_key']);
            $organizer->setEncryptedAttribute('email_encrypted', $data['organizer_email'], $encryption['master_key']);

            // Save name hash to allow uniqueness verification
            $organizer->name_hash = hash('sha256', strtolower($data['organizer_name']));

            $organizer->save();

            // 4. Generate organizer link
            $organizerLink = $this->encryptionManager->getIndividualKeyManager()
                ->generateParticipantLink(
                    config('app.url'),
                    $draw->uuid,
                    $organizer->uuid,
                    $encryption['organizer_key']
                );

            DB::commit();

            Log::info("Draw created", ['draw_uuid' => $draw->uuid, 'user_id' => $user?->id]);

            return [
                'success' => true,
                'draw' => $draw,
                'organizer_link' => $organizerLink,
                'master_key' => $encryption['master_key'], // To be stored temporarily on client ifof
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to create draw", ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'error' => 'Failed to create draw: ' . $e->getMessage()
            ];
        }
    }
}
