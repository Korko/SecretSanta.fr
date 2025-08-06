<?php

namespace App\Actions\Draw;


use App\Models\Draw\Draw;
use Illuminate\Support\Facades\Log;

/**
 * Action pour révéler les résultats du tirage
 */
class RevealDrawAction
{
    public function execute(Draw $draw, string $masterKey): array
    {
        try {
            if ($draw->status !== 'drawn') {
                throw new \Exception('Draw must be completed before revealing');
            }

            // Récupérer tous les appariements
            $assignments = [];
            $participants = $draw->acceptedParticipants()->with('assignedTo')->get();

            foreach ($participants as $participant) {
                if ($participant->assignedTo) {
                    $assignments[] = [
                        'giver' => [
                            'uuid' => $participant->uuid,
                            'name' => $participant->getDecryptedAttribute('name_encrypted', $masterKey)
                        ],
                        'receiver' => [
                            'uuid' => $participant->assignedTo->uuid,
                            'name' => $participant->assignedTo->getDecryptedAttribute('name_encrypted', $masterKey)
                        ]
                    ];
                }
            }

            // Marquer comme révélé
            $draw->reveal();

            Log::info("Draw revealed", ['draw_uuid' => $draw->uuid]);

            return [
                'success' => true,
                'message' => 'Draw results revealed',
                'assignments' => $assignments,
                'draw' => $draw
            ];

        } catch (\Exception $e) {
            Log::error("Failed to reveal draw", [
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
