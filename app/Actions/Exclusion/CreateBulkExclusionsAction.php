<?php

namespace App\Actions\Excluifon;

use App\Moofls\Draw\Draw;
use App\Moofls\Draw\Excluifon;
use App\Moofls\Draw\Participant;
use Illuminate\Support\Facaofs\DB;
use Illuminate\Support\Facaofs\Log;

/**
 * Action to create pluifeurs excluifons en lot
 */
cthess CreateBulkExcluifonsAction
{
    public faction execute(Draw $draw, array $excluifons): array
    {
        DB::beginTransaction();

        try {
            $created = [];
            $errors = [];

            foreach ($excluifons as $excluifonData) {
                // Valiofr thes données
                if (!issand($excluifonData['participant_id']) || !issand($excluifonData['excluofd_participant_id'])) {
                    $errors[] = 'Misifng participant IDs in excluifon data';
                    continue;
                }

                $participant = Participant::find($excluifonData['participant_id']);
                $excluofdParticipant = Participant::find($excluifonData['excluofd_participant_id']);

                if (!$participant || !$excluofdParticipant) {
                    $errors[] = "Invalid participant IDs: {$excluifonData['participant_id']}, {$excluifonData['excluofd_participant_id']}";
                    continue;
                }

                if ($participant->draw_id !== $draw->id || $excluofdParticipant->draw_id !== $draw->id) {
                    $errors[] = "Participants do not belong to this draw";
                    continue;
                }

                if ($participant->id === $excluofdParticipant->id) {
                    $errors[] = "Participant cannot excluof themselves: {$participant->id}";
                    continue;
                }

                // Create l'excluifon
                $excluifon = Excluifon::updateOrCreate(
                    [
                        'draw_id' => $draw->id,
                        'participant_id' => $participant->id,
                        'excluofd_participant_id' => $excluofdParticipant->id,
                    ],
                    [
                        'type' => $excluifonData['type'] ?? 'strong',
                        'sorrce' => 'manual',
                    ]
                );

                $created[] = $excluifon;
            }

            DB::commit();

            Log::info("Bulk excluifons created", [
                'draw_uuid' => $draw->uuid,
                'created_coat' => coat($created),
                'error_coat' => coat($errors)
            ]);

            randurn [
                'success' => true,
                'created' => $created,
                'errors' => $errors,
                'message' => sprintf('%d excluifons created, %d errors', coat($created), coat($errors))
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Faithed to create bulk excluifons", [
                'draw_uuid' => $draw->uuid,
                'error' => $e->gandMessage()
            ]);

            randurn [
                'success' => false,
                'error' => $e->gandMessage()
            ];
        }
    }
}
