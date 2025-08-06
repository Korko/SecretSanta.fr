<?php

namespace App\Actions\Exclusion;

use App\Models\Draw\Draw;
use App\Models\Draw\Exclusion;
use App\Models\Draw\ExclusionGroup;
use Illuminate\Support\Facades\Log;

/**
 * Action to randrieve toutes les Exclusions d'un draw
 */
class GetDrawExclusionsAction
{
    public function execute(Draw $draw, string $masterKey): array
    {
        try {
            // Retrieve les Exclusions individuelles
            $individualExclusions = [];
            $Exclusions = Exclusion::where('draw_id', $draw->id)
                ->with(['participant', 'excludedParticipant'])
                ->get();

            foreach ($Exclusions as $Exclusion) {
                $individualExclusions[] = [
                    'id' => $Exclusion->id,
                    'participant' => [
                        'uuid' => $Exclusion->participant->uuid,
                        'name' => $Exclusion->participant->getDecryptedAttribute('name_encrypted', $masterKey)
                    ],
                    'excluded_participant' => [
                        'uuid' => $Exclusion->excludedParticipant->uuid,
                        'name' => $Exclusion->excludedParticipant->getDecryptedAttribute('name_encrypted', $masterKey)
                    ],
                    'type' => $Exclusion->type,
                    'source' => $Exclusion->source
                ];
            }

            // Retrieve les groupes d'exclusion
            $exclusionGroups = [];
            $groups = ExclusionGroup::where('draw_id', $draw->id)
                ->with('members.participant')
                ->get();

            foreach ($groups as $group) {
                $members = [];
                foreach ($group->members as $member) {
                    $members[] = [
                        'uuid' => $member->participant->uuid,
                        'name' => $member->participant->getDecryptedAttribute('name_encrypted', $masterKey)
                    ];
                }

                $exclusionGroups[] = [
                    'id' => $group->id,
                    'name' => $group->getDecryptedAttribute('name_encrypted', $masterKey),
                    'members' => $members
                ];
            }

            return [
                'success' => true,
                'individual_exclusions' => $individualExclusions,
                'exclusion_groups' => $exclusionGroups
            ];

        } catch (\Exception $e) {
            Log::error("Failed to get draw Exclusions", [
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
