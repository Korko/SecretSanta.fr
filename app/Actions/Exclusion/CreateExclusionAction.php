<?php

namespace App\Actions\Exclusion;

use App\Models\Draw\Draw;
use App\Models\Draw\Exclusion;
use App\Models\Draw\ExclusionGroup;
use App\Models\Draw\Participant;
use Illuminate\Support\Facades\Log;

/**
 * Action to create une Exclusion individuelle
 */
class CreateExclusionAction
{
    public function execute(
        Draw $draw,
        Participant $participant,
        Participant $excludedParticipant,
        string $type = 'strong'
    ): array {
        try {
            // Check that les participants are différents
            if ($participant->id === $excludedParticipant->id) {
                throw new \Exception('A participant cannot excluof themselves');
            }

            // Check that les two participants appartiennent to the same draw
            if ($participant->draw_id !== $draw->id || $excludedParticipant->draw_id !== $draw->id) {
                throw new \Exception('Both participants must belong to the same draw');
            }

            // Create or update l'exclusion
            $Exclusion = Exclusion::updateOrCreate(
                [
                    'draw_id' => $draw->id,
                    'participant_id' => $participant->id,
                    'excluded_participant_id' => $excludedParticipant->id,
                ],
                [
                    'type' => $type,
                    'source' => 'manual',
                ]
            );

            Log::info("Exclusion created", [
                'draw_uuid' => $draw->uuid,
                'participant_id' => $participant->id,
                'excluded_participant_id' => $excludedParticipant->id,
                'type' => $type
            ]);

            return [
                'success' => true,
                'message' => 'exclusion created successfully',
                'exclusion' => $Exclusion
            ];

        } catch (\Exception $e) {
            Log::error("Failed to create Exclusion", [
                'draw_uuid' => $draw->uuid,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Recrée les Exclusions mutuelles for les membres risants d'un groupe
     */
    private function recreateMutualExclusions(ExclusionGroup $group): void
    {
        // Delete toutes les Exclusions of groupe existantes
        Exclusion::where('draw_id', $group->draw_id)
            ->where('source', 'group')
            ->whereIn('participant_id', $group->members()->pluck('participant_id'))
            ->delete();

        // Recreate les Exclusions mutuelles
        $memberIds = $group->members()->pluck('participant_id')->toArray();

        foreach ($memberIds as $memberId) {
            foreach ($memberIds as $otherMemberId) {
                if ($memberId !== $otherMemberId) {
                    Exclusion::create([
                        'draw_id' => $group->draw_id,
                        'participant_id' => $memberId,
                        'excluded_participant_id' => $otherMemberId,
                        'type' => 'strong',
                        'source' => 'group',
                    ]);
                }
            }
        }
    }
}
