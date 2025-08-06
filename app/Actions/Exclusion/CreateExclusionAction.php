<?php

namespace App\Actions\Exclusion;

use App\Models\Draw\Draw;
use App\Models\Draw\Exclusion;
use App\Models\Draw\ExclusionGroup;
use App\Models\Draw\Participant;
use Illuminate\Support\Facades\Log;

/**
 * Action pour créer une exclusion individuelle
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
            // Vérifier que les participants sont différents
            if ($participant->id === $excludedParticipant->id) {
                throw new \Exception('A participant cannot exclude themselves');
            }

            // Vérifier que les deux participants appartiennent au même tirage
            if ($participant->draw_id !== $draw->id || $excludedParticipant->draw_id !== $draw->id) {
                throw new \Exception('Both participants must belong to the same draw');
            }

            // Créer ou mettre à jour l'exclusion
            $exclusion = Exclusion::updateOrCreate(
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
                'message' => 'Exclusion created successfully',
                'exclusion' => $exclusion
            ];

        } catch (\Exception $e) {
            Log::error("Failed to create exclusion", [
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
     * Recrée les exclusions mutuelles pour les membres restants d'un groupe
     */
    private function recreateMutualExclusions(ExclusionGroup $group): void
    {
        // Supprimer toutes les exclusions de groupe existantes
        Exclusion::where('draw_id', $group->draw_id)
            ->where('source', 'group')
            ->whereIn('participant_id', $group->members()->pluck('participant_id'))
            ->delete();

        // Recréer les exclusions mutuelles
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
