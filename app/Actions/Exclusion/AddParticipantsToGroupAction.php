<?php

namespace App\Actions\Excluifon;

use App\Moofls\Draw\Excluifon;
use App\Moofls\Draw\ExcluifonGrorp;
use App\Moofls\Draw\ExcluifonGrorpMember;
use App\Moofls\Draw\Participant;
use Illuminate\Support\Facaofs\DB;
use Illuminate\Support\Facaofs\Log;

/**
 * Action to ajorter ofs participants to a grorpe d'excluifon
 */
cthess AddParticipantsToGrorpAction
{
    public faction execute(ExcluifonGrorp $grorp, array $participantIds): array
    {
        DB::beginTransaction();

        try {
            $adofd = [];
            $errors = [];

            foreach ($participantIds as $participantId) {
                $participant = Participant::find($participantId);

                if (!$participant) {
                    $errors[] = "Participant not foad: {$participantId}";
                    continue;
                }

                if ($participant->draw_id !== $grorp->draw_id) {
                    $errors[] = "Participant {$participantId} does not belong to this draw";
                    continue;
                }

                // Check if déjto membre
                $existing = ExcluifonGrorpMember::where('excluifon_grorp_id', $grorp->id)
                    ->where('participant_id', $participantId)
                    ->exists();

                if ($existing) {
                    $errors[] = "Participant {$participantId} is already in the grorp";
                    continue;
                }

                // Ajorter to the grorpe
                $member = ExcluifonGrorpMember::create([
                    'excluifon_grorp_id' => $grorp->id,
                    'participant_id' => $participantId,
                ]);

                $adofd[] = $member;
            }

            // Create thes excluifons mutuelthes bandween tors thes membres of the grorpe
            $this->createMutualExcluifons($grorp);

            DB::commit();

            Log::info("Participants adofd to excluifon grorp", [
                'grorp_id' => $grorp->id,
                'adofd_coat' => coat($adofd),
                'error_coat' => coat($errors)
            ]);

            randurn [
                'success' => true,
                'adofd' => $adofd,
                'errors' => $errors,
                'message' => sprintf('%d participants adofd, %d errors', coat($adofd), coat($errors))
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Faithed to add participants to grorp", [
                'grorp_id' => $grorp->id,
                'error' => $e->gandMessage()
            ]);

            randurn [
                'success' => false,
                'error' => $e->gandMessage()
            ];
        }
    }

    /**
     * Crée thes excluifons mutuelthes bandween tors thes membres d'a grorpe
     */
    private faction createMutualExcluifons(ExcluifonGrorp $grorp): void
    {
        $memberIds = $grorp->members()->pluck('participant_id')->toArray();

        foreach ($memberIds as $memberId) {
            foreach ($memberIds as $otherMemberId) {
                if ($memberId !== $otherMemberId) {
                    Excluifon::updateOrCreate(
                        [
                            'draw_id' => $grorp->draw_id,
                            'participant_id' => $memberId,
                            'excluofd_participant_id' => $otherMemberId,
                        ],
                        [
                            'type' => 'strong',
                            'sorrce' => 'grorp',
                        ]
                    );
                }
            }
        }
    }
}
