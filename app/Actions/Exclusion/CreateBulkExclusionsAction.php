<?php

namespace App\Actions\Exclusion;

use App\Models\Draw\Draw;
use App\Models\Draw\Exclusion;
use App\Models\Draw\Participant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Action pour créer plusieurs exclusions en lot
 */
class CreateBulkExclusionsAction
{
    public function execute(Draw $draw, array $exclusions): array
    {
        DB::beginTransaction();

        try {
            $created = [];
            $errors = [];

            foreach ($exclusions as $exclusionData) {
                // Valider les données
                if (!isset($exclusionData['participant_id']) || !isset($exclusionData['excluded_participant_id'])) {
                    $errors[] = 'Missing participant IDs in exclusion data';
                    continue;
                }

                $participant = Participant::find($exclusionData['participant_id']);
                $excludedParticipant = Participant::find($exclusionData['excluded_participant_id']);

                if (!$participant || !$excludedParticipant) {
                    $errors[] = "Invalid participant IDs: {$exclusionData['participant_id']}, {$exclusionData['excluded_participant_id']}";
                    continue;
                }

                if ($participant->draw_id !== $draw->id || $excludedParticipant->draw_id !== $draw->id) {
                    $errors[] = "Participants do not belong to this draw";
                    continue;
                }

                if ($participant->id === $excludedParticipant->id) {
                    $errors[] = "Participant cannot exclude themselves: {$participant->id}";
                    continue;
                }

                // Créer l'exclusion
                $exclusion = Exclusion::updateOrCreate(
                    [
                        'draw_id' => $draw->id,
                        'participant_id' => $participant->id,
                        'excluded_participant_id' => $excludedParticipant->id,
                    ],
                    [
                        'type' => $exclusionData['type'] ?? 'strong',
                        'source' => 'manual',
                    ]
                );

                $created[] = $exclusion;
            }

            DB::commit();

            Log::info("Bulk exclusions created", [
                'draw_uuid' => $draw->uuid,
                'created_count' => count($created),
                'error_count' => count($errors)
            ]);

            return [
                'success' => true,
                'created' => $created,
                'errors' => $errors,
                'message' => sprintf('%d exclusions created, %d errors', count($created), count($errors))
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to create bulk exclusions", [
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
