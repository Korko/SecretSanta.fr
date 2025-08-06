<?php

namespace App\Actions\Draw;

use App\Models\Draw\Draw;
use Illuminate\Support\Facades\Log;

/**
 * Action pour récupérer les informations d'un tirage avec déchiffrement
 */
class GetDrawDetailsAction
{
    public function execute(Draw $draw, string $masterKey): array
    {
        try {
            // Déchiffrer les informations du tirage
            $drawData = [
                'uuid' => $draw->uuid,
                'title' => $draw->getDecryptedAttribute('title_encrypted', $masterKey),
                'description' => $draw->getDecryptedAttribute('description_encrypted', $masterKey),
                'organizer_name' => $draw->getDecryptedAttribute('organizer_name_encrypted', $masterKey),
                'organizer_email' => $draw->getDecryptedAttribute('organizer_email_encrypted', $masterKey),
                'status' => $draw->status,
                'registration_deadline' => $draw->registration_deadline,
                'auto_accept_participants' => $draw->auto_accept_participants,
                'allow_target_messages' => $draw->allow_target_messages,
                'created_at' => $draw->created_at,
                'drawn_at' => $draw->drawn_at,
                'revealed_at' => $draw->revealed_at,
            ];

            // Récupérer les participants
            $participants = [];
            foreach ($draw->participants as $participant) {
                $participants[] = [
                    'uuid' => $participant->uuid,
                    'name' => $participant->getDecryptedAttribute('name_encrypted', $masterKey),
                    'email' => $participant->getDecryptedAttribute('email_encrypted', $masterKey),
                    'status' => $participant->status,
                    'is_organizer' => $participant->is_organizer,
                    'accepted_at' => $participant->accepted_at,
                ];
            }

            return [
                'success' => true,
                'draw' => $drawData,
                'participants' => $participants
            ];

        } catch (\Exception $e) {
            Log::error("Failed to get draw details", [
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
