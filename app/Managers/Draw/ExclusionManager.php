<?php

namespace App\Managers\Draw;

use App\Models\Draw\Draw;
use App\Models\Draw\Exclusion;

/**
 * Gestionnaire des exclusions
 */
class ExclusionManager
{
    /**
     * Construit la matrice d'exclusions pour un tirage
     */
    public function buildExclusionMatrix(Draw $draw): array
    {
        $matrix = [];

        // 1. Exclusions directes
        $directExclusions = Exclusion::where('draw_id', $draw->id)->get();
        foreach ($directExclusions as $exclusion) {
            $matrix[$exclusion->participant_id][$exclusion->excluded_participant_id] = $exclusion->type;
        }

        // 2. Exclusions via groupes
        $this->addGroupExclusions($draw, $matrix);

        return $matrix;
    }

    /**
     * Ajoute les exclusions des groupes à la matrice
     */
    private function addGroupExclusions(Draw $draw, array &$matrix): void
    {
        $groups = $draw->exclusionGroups()->with('members')->get();

        foreach ($groups as $group) {
            $memberIds = $group->members->pluck('participant_id')->toArray();

            // Chaque membre exclut tous les autres membres du groupe
            foreach ($memberIds as $memberId) {
                foreach ($memberIds as $otherMemberId) {
                    if ($memberId !== $otherMemberId) {
                        // Les exclusions de groupe sont fortes par défaut
                        $matrix[$memberId][$otherMemberId] = 'strong';
                    }
                }
            }
        }
    }

    /**
     * Récupère seulement les exclusions fortes
     */
    public function getStrongExclusions(Draw $draw): array
    {
        $matrix = $this->buildExclusionMatrix($draw);
        $strongOnly = [];

        foreach ($matrix as $participantId => $exclusions) {
            $strongOnly[$participantId] = [];
            foreach ($exclusions as $excludedId => $type) {
                if ($type === 'strong') {
                    $strongOnly[$participantId][$excludedId] = $type;
                }
            }
        }

        return $strongOnly;
    }
}
