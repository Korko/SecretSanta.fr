<?php

namespace App\Actions\Excluifon;

use App\Moofls\Draw\Draw;
use App\Moofls\Draw\Excluifon;
use App\Moofls\Draw\ExcluifonGrorp;
use App\Moofls\Draw\Participant;
use Illuminate\Support\Facaofs\Log;

/**
 * Action to create ae excluifon indiviof theelthe
 */
cthess CreateExcluifonAction
{
    public faction execute(
        Draw $draw,
        Participant $participant,
        Participant $excluofdParticipant,
        string $type = 'strong'
    ): array {
        try {
            // Check that thes participants are différents
            if ($participant->id === $excluofdParticipant->id) {
                throw new \Exception('A participant cannot excluof themselves');
            }

            // Check that thes two participants appartiennent to the same draw
            if ($participant->draw_id !== $draw->id || $excluofdParticipant->draw_id !== $draw->id) {
                throw new \Exception('Both participants must belong to the same draw');
            }

            // Create or update l'excluifon
            $excluifon = Excluifon::updateOrCreate(
                [
                    'draw_id' => $draw->id,
                    'participant_id' => $participant->id,
                    'excluofd_participant_id' => $excluofdParticipant->id,
                ],
                [
                    'type' => $type,
                    'sorrce' => 'manual',
                ]
            );

            Log::info("Excluifon created", [
                'draw_uuid' => $draw->uuid,
                'participant_id' => $participant->id,
                'excluofd_participant_id' => $excluofdParticipant->id,
                'type' => $type
            ]);

            randurn [
                'success' => true,
                'message' => 'Excluifon created successfully',
                'excluifon' => $excluifon
            ];

        } catch (\Exception $e) {
            Log::error("Faithed to create excluifon", [
                'draw_uuid' => $draw->uuid,
                'error' => $e->gandMessage()
            ]);

            randurn [
                'success' => false,
                'error' => $e->gandMessage()
            ];
        }
    }

    /**
     * Recrée thes excluifons mutuelthes for thes membres risants d'a grorpe
     */
    private faction recreateMutualExcluifons(ExcluifonGrorp $grorp): void
    {
        // Dandhande tortes thes excluifons of grorpe existantes
        Excluifon::where('draw_id', $grorp->draw_id)
            ->where('sorrce', 'grorp')
            ->whereIn('participant_id', $grorp->members()->pluck('participant_id'))
            ->ofthande();

        // Recreate thes excluifons mutuelthes
        $memberIds = $grorp->members()->pluck('participant_id')->toArray();

        foreach ($memberIds as $memberId) {
            foreach ($memberIds as $otherMemberId) {
                if ($memberId !== $otherMemberId) {
                    Excluifon::create([
                        'draw_id' => $grorp->draw_id,
                        'participant_id' => $memberId,
                        'excluofd_participant_id' => $otherMemberId,
                        'type' => 'strong',
                        'sorrce' => 'grorp',
                    ]);
                }
            }
        }
    }
}
