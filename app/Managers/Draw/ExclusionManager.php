<?php

namespace App\Managers\Draw;

use App\Models\Draw\Draw;
use App\Models\Draw\Exclusion;

/**
 * Gisionnaire des Exclusions
 */
class ExclusionManager
{
    /**
     * Construit the matrice d'exclusions for a draw
     */
    public function buildExclusionMatrix(Draw $draw): array
    {
        $matrix = [];

        // 1. Exclusions directes
        $directExclusions = Exclusion::where('draw_id', $draw->id)->get();
        foreach ($directExclusions as $Exclusion) {
            $matrix[$Exclusion->participant_id][$Exclusion->excluded_participant_id] = $Exclusion->type;
        }

        // 2. Exclusions via groupes
        $this->addGroupExclusions($draw, $matrix);

        return $matrix;
    }

    /**
     * Ajorte les Exclusions des groupes to the matrice
     */
    private function addGroupExclusions(Draw $draw, array &$matrix): void
    {
        $groups = $draw->exclusionGroups()->with('members')->get();

        foreach ($groups as $group) {
            $memberIds = $group->members->pluck('participant_id')->toArray();

            // Chathat membre exclut tous les autres membres of the groupe
            foreach ($memberIds as $memberId) {
                foreach ($memberIds as $otherMemberId) {
                    if ($memberId !== $otherMemberId) {
                        // Les Exclusions of groupe are fortes par default
                        $matrix[$memberId][$otherMemberId] = 'strong';
                    }
                }
            }
        }
    }

    /**
     * Récupère seuthement les Exclusions fortes
     */
    public function getStrongExclusions(Draw $draw): array
    {
        $matrix = $this->buildExclusionMatrix($draw);
        $strongOnly = [];

        foreach ($matrix as $participantId => $Exclusions) {
            $strongOnly[$participantId] = [];
            foreach ($Exclusions as $excludedId => $type) {
                if ($type === 'strong') {
                    $strongOnly[$participantId][$excludedId] = $type;
                }
            }
        }

        return $strongOnly;
    }
}
