<?php

namespace App\Solvers;

use Generator;
use Illuminate\Support\Collection;

class HatSolver extends Solver
{
    protected function solve(Collection $participantsIdx, Collection $exclusions): Generator
    {
        return $this->solveWithExclusions(participantIdx: (int) $participantsIdx->first(), exclusions: $exclusions, hat: $participantsIdx->shuffle());
    }

    private function solveWithExclusions(int $participantIdx, array $combination, Collection $exclusions, Collection $hat): Generator
    {
        // End of a loop, we've found a possible combination
        if ($hat->isEmpty()) {
            yield $combination;
        }

        if (isset($combination[$participantIdx])) {
            yield from $this->solveWithExclusions($participantIdx + 1, $combination, $exclusions, $hat);
        }

        // Get the exclusions requested for that participant
        // And remove them from the hat (+ the current participant)
        $participantExclusions = $exclusions->get($participantIdx, []);
        $hat = $hat
            ->diff($participantExclusions)
            ->diff([$participantIdx]);

        // If nothing available in the hat for that participant
        // Then nothing will happen and the combination will be lost
        foreach ($hat as $drawnParticipant) {
            // Create a new possible solution with the participant drawn
            $newCombination = $combination + [$participantIdx => $drawnParticipant];

            $newHat = $hat->diff([$drawnParticipant]);

            // Further check if this solution is possible
            yield from $this->solveWithExclusions(
                participantIdx: $participantIdx + 1,
                combination: $newCombination,
                exclusions: $exclusions,
                hat: $newHat
            );
        }
    }
}
