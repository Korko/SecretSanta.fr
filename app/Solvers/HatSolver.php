<?php

namespace App\Solvers;

use Generator;
use Illuminate\Support\Collection;

class HatSolver extends Solver
{
    protected function solve(Collection $participantsIdx, Collection $exclusions): Generator
    {
        return $this->solveWithExclusions((int) $participantsIdx->first(), [], $exclusions, $participantsIdx->shuffle());
    }

    private function solveWithExclusions(int $participantIdx, array $combination, Collection $allExclusions, Collection $currentHat): Generator
    {
        // End of a loop, we've found a possible combination
        if ($currentHat->isEmpty()) {
            yield $combination;
        }

        if (isset($combination[$participantIdx])) {
            yield from $this->solveWithExclusions($participantIdx + 1, $combination, $allExclusions, $currentHat);
        }

        // Get the exclusions requested for that participant
        // And remove them from the hat (+ the current participant)
        $exclusions = $allExclusions->get($participantIdx, []);
        $hat = $currentHat
            ->diff($exclusions)
            ->diff([$participantIdx]);

        // If nothing available in the hat for that participant
        // Then nothing will happen and the combination will be lost
        foreach ($hat as $drawnParticipant) {
            // Create a new possible solution with the participant drawn
            $newCombination = $combination + [$participantIdx => $drawnParticipant];

            $newHat = $currentHat->diff([$drawnParticipant]);

            // Further check if this solution is possible
            yield from $this->solveWithExclusions(
                $participantIdx + 1,
                $newCombination,
                $allExclusions,
                $newHat
            );
        }
    }
}
