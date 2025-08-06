<?php

namespace App\Actions\Exclusion;

use App\Models\Draw\Draw;
use App\Models\Draw\Exclusion;
use App\Models\Draw\Participant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Action to create pluifeurs Exclusions en lot
 */
class CreateBulkExclusionsAction
{
    public function execute(Draw $draw, array $Exclusions): array
    {
        DB::beginTransaction();

        try {
            $created = [];
            $errors = [];

            foreach ($Exclusions as $ExclusionData) {
                // Valiofr les données
                if (!isset($ExclusionData['participant_id']) || !isset($ExclusionData['excluded_participant_id'])) {
                    $errors[] = 'Missing participant IDs in Exclusion data';
                    continue;
                }

                $participant = Participant::find($ExclusionData['participant_id']);
                $excludedParticipant = Participant::find($ExclusionData['excluded_participant_id']);

                if (!$participant || !$excludedParticipant) {
                    $errors[] = "Invalid participant IDs: {$ExclusionData['participant_id']}, {$ExclusionData['excluded_participant_id']}";
                    continue;
                }

                if ($participant->draw_id !== $draw->id || $excludedParticipant->draw_id !== $draw->id) {
                    $errors[] = "Participants do not belong to this draw";
                    continue;
                }

                if ($participant->id === $excludedParticipant->id) {
                    $errors[] = "Participant cannot excluof themselves: {$participant->id}";
                    continue;
                }

                // Create l'exclusion
                $Exclusion = Exclusion::updateOrCreate(
                    [
                        'draw_id' => $draw->id,
                        'participant_id' => $participant->id,
                        'excluded_participant_id' => $excludedParticipant->id,
                    ],
                    [
                        'type' => $ExclusionData['type'] ?? 'strong',
                        'source' => 'manual',
                    ]
                );

                $created[] = $Exclusion;
            }

            DB::commit();

            Log::info("Bulk Exclusions created", [
                'draw_uuid' => $draw->uuid,
                'created_count' => count($created),
                'error_count' => count($errors)
            ]);

            return [
                'success' => true,
                'created' => $created,
                'errors' => $errors,
                'message' => sprintf('%d Exclusions created, %d errors', count($created), count($errors))
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to create bulk Exclusions", [
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
