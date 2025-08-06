<?php

namespace App\Services\Draw;

use App\Results\DrawResult;
use Illuminate\Support\Collection;

/**
 * Algorithme de tirage au sort avec backtracking
 */
class DrawAlgorithm
{
    private int $maxAttempts = 1000;
    private array $assignments = [];
    private array $available = [];
    private array $exclusions = [];

    /**
     * Effectue le tirage avec backtracking
     */
    public function performDraw(Collection $participants, array $exclusions): DrawResult
    {
        $this->exclusions = $exclusions;
        $this->assignments = [];
        $this->available = $participants->pluck('id')->toArray();

        // Trier les participants par nombre d'exclusions (desc)
        $sortedParticipants = $this->sortParticipantsByConstraints($participants);

        $startTime = microtime(true);
        $success = $this->backtrack($sortedParticipants, 0);
        $duration = microtime(true) - $startTime;

        if ($success) {
            return DrawResult::successful($this->assignments, $duration);
        } else {
            return DrawResult::failed(['Impossible to find a valid assignment'], $duration);
        }
    }

    /**
     * Trie les participants par nombre de contraintes (le plus contraint en premier)
     */
    private function sortParticipantsByConstraints(Collection $participants): array
    {
        return $participants->sortByDesc(function ($participant) {
            return count($this->exclusions[$participant->id] ?? []);
        })->pluck('id')->toArray();
    }

    /**
     * Algorithme de backtracking récursif
     */
    private function backtrack(array $participantIds, int $index): bool
    {
        // Cas de base : tous les participants sont assignés
        if ($index >= count($participantIds)) {
            return true;
        }

        $currentParticipantId = $participantIds[$index];
        $possibleTargets = $this->getPossibleTargets($currentParticipantId);

        // Mélanger pour éviter les patterns
        shuffle($possibleTargets);

        foreach ($possibleTargets as $targetId) {
            // Assigner temporairement
            $this->assignments[$currentParticipantId] = $targetId;
            $this->removeFromAvailable($targetId);

            // Continuer récursivement
            if ($this->backtrack($participantIds, $index + 1)) {
                return true; // Solution trouvée
            }

            // Backtrack : annuler l'assignation
            unset($this->assignments[$currentParticipantId]);
            $this->addToAvailable($targetId);
        }

        return false; // Aucune solution trouvée à ce niveau
    }

    /**
     * Récupère les cibles possibles pour un participant
     */
    private function getPossibleTargets(int $participantId): array
    {
        $possible = [];
        $participantExclusions = $this->exclusions[$participantId] ?? [];

        foreach ($this->available as $targetId) {
            // Ne peut pas s'assigner à soi-même
            if ($targetId === $participantId) {
                continue;
            }

            $exclusionType = $participantExclusions[$targetId] ?? null;

            // Respecter les exclusions fortes
            if ($exclusionType === 'strong') {
                continue;
            }

            $possible[] = $targetId;
        }

        return $possible;
    }

    /**
     * Récupère les cibles possibles en ignorant les exclusions faibles si nécessaire
     */
    private function getPossibleTargetsIgnoringWeak(int $participantId): array
    {
        $possible = [];
        $participantExclusions = $this->exclusions[$participantId] ?? [];

        foreach ($this->available as $targetId) {
            if ($targetId === $participantId) {
                continue;
            }

            $exclusionType = $participantExclusions[$targetId] ?? null;

            // Ignorer seulement les exclusions fortes
            if ($exclusionType === 'strong') {
                continue;
            }

            $possible[] = $targetId;
        }

        return $possible;
    }

    /**
     * Retire un participant de la liste des disponibles
     */
    private function removeFromAvailable(int $participantId): void
    {
        $key = array_search($participantId, $this->available);
        if ($key !== false) {
            unset($this->available[$key]);
        }
    }

    /**
     * Ajoute un participant à la liste des disponibles
     */
    private function addToAvailable(int $participantId): void
    {
        if (!in_array($participantId, $this->available)) {
            $this->available[] = $participantId;
        }
    }
}
