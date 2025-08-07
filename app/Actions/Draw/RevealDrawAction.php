<?php

namespace App\Actions\Draw;


use App\Models\Draw\Draw;
use Illuminate\Support\Facades\Log;

/**
 * Action to reveal draw results
 */
class RevealDrawAction
{
    public function execute(Draw $draw, string $masterKey): array
    {
        try {
            if ($draw->status !== 'drawn') {
                throw new \Exception('Draw must be completed before revealing');
            }

            // Retrieve all assignments
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

            // Mark as revealed
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
