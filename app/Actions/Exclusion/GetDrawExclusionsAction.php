<?php

namespace App\Actions\Exclusion;

use App\Models\Draw\Draw;
use App\Models\Draw\Exclusion;
use App\Models\Draw\ExclusionGroup;
use Illuminate\Support\Facades\Log;

/**
 * Action pour récupérer toutes les exclusions d'un tirage
 */
class GetDrawExclusionsAction
{
    public function execute(Draw $draw, string $masterKey): array
    {
        try {
            // Récupérer les exclusions individuelles
            $individualExclusions = [];
            $exclusions = Exclusion::where('draw_id', $draw->id)
                ->with(['participant', 'excludedParticipant'])
                ->get();

            foreach ($exclusions as $exclusion) {
                $individualExclusions[] = [
                    'id' => $exclusion->id,
                    'participant' => [
                        'uuid' => $exclusion->participant->uuid,
                        'name' => $exclusion->participant->getDecryptedAttribute('name_encrypted', $masterKey)
                    ],
                    'excluded_participant' => [
                        'uuid' => $exclusion->excludedParticipant->uuid,
                        'name' => $exclusion->excludedParticipant->getDecryptedAttribute('name_encrypted', $masterKey)
                    ],
                    'type' => $exclusion->type,
                    'source' => $exclusion->source
                ];
            }

            // Récupérer les groupes d'exclusion
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
            Log::error("Failed to get draw exclusions", [
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
