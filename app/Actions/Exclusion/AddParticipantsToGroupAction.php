<?php

namespace App\Actions\Exclusion;

use App\Models\Draw\Exclusion;
use App\Models\Draw\ExclusionGroup;
use App\Models\Draw\ExclusionGroupMember;
use App\Models\Draw\Participant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Action to add participants to a group of exclusion
 */
class AddParticipantsToGroupAction
{
    public function execute(ExclusionGroup $group, array $participantIds): array
    {
        DB::beginTransaction();

        try {
            $added = [];
            $errors = [];

            foreach ($participantIds as $participantId) {
                $participant = Participant::find($participantId);

                if (!$participant) {
                    $errors[] = "Participant not foad: {$participantId}";
                    continue;
                }

                if ($participant->draw_id !== $group->draw_id) {
                    $errors[] = "Participant {$participantId} does not belong to this draw";
                    continue;
                }

                // Check if already member
                $existing = ExclusionGroupMember::where('exclusion_group_id', $group->id)
                    ->where('participant_id', $participantId)
                    ->exists();

                if ($existing) {
                    $errors[] = "Participant {$participantId} is already in the group";
                    continue;
                }

                // Add to the group
                $member = ExclusionGroupMember::create([
                    'exclusion_group_id' => $group->id,
                    'participant_id' => $participantId,
                ]);

                $added[] = $member;
            }

            // Create the mutual exclusions between all the members of the group
            $this->createMutualExclusions($group);

            DB::commit();

            Log::info("Participants added to Exclusion group", [
                'group_id' => $group->id,
                'added_count' => count($added),
                'error_count' => count($errors)
            ]);

            return [
                'success' => true,
                'added' => $added,
                'errors' => $errors,
                'message' => sprintf('%d participants added, %d errors', count($added), count($errors))
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to add participants to group", [
                'group_id' => $group->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Crée les Exclusions mutuelles between tous les membres d'un groupe
     */
    private function createMutualExclusions(ExclusionGroup $group): void
    {
        $memberIds = $group->members()->pluck('participant_id')->toArray();

        foreach ($memberIds as $memberId) {
            foreach ($memberIds as $otherMemberId) {
                if ($memberId !== $otherMemberId) {
                    Exclusion::updateOrCreate(
                        [
                            'draw_id' => $group->draw_id,
                            'participant_id' => $memberId,
                            'excluded_participant_id' => $otherMemberId,
                        ],
                        [
                            'type' => 'strong',
                            'source' => 'group',
                        ]
                    );
                }
            }
        }
    }
}
