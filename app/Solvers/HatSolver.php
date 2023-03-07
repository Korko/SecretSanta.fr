<?php

namespace App\Solvers;

use Generator;
use Illuminate\Support\Collection;

class HatSolver extends Solver
{
    protected function solve(Collection $participantsIdx, Collection $exclusions): Generator
    {
        $participantsIdx->sortKeysUsing(function ($participantIdx1, $participantIdx2) use ($exclusions) {
            // Reverse sort by number of exclusions (nothing like !<=> atm)
            $count1 = count(Arr::get($exclusions, $participantIdx1, []));
            $count2 = count(Arr::get($exclusions, $participantIdx2, []));

            return $count1 > $count2 ? -1 : (
                $count1 === $count2 ? 0 : 1
            );
        });

        return $this->solveWithExclusions(participantsIdx: $participantsIdx, currentIdx: (int) $participantsIdx->first(), exclusions: $exclusions, hat: $participantsIdx->shuffle());
    }

    private function solveWithExclusions(Collection $participantsIdx, int $currentIdx, Collection $exclusions, Collection $hat, array $combination = []): Generator
    {
        // End of a loop, we've found a possible combination
        if ($hat->isEmpty()) {
            ksort($combination);

            return yield $combination;
        }

        $participantIdx = $participantsIdx[$currentIdx];

        if (isset($combination[$participantIdx])) {
            yield from $this->solveWithExclusions(
                participantsIdx: $participantsIdx,
                currentIdx: $currentIdx + 1,
                exclusions: $exclusions,
                hat: $hat,
                combination: $combination,
            );
        }

        // Get the exclusions requested for that participant
        // And remove them from the hat (+ the current participant)
        $participantExclusions = $exclusions->get($participantIdx, []);
        $potentialParticipants = clone $hat
            ->diff($participantExclusions)
            ->diff([$participantIdx]);

        // If nothing available in the hat for that participant
        // Then nothing will happen and the combination will be lost
        foreach ($potentialParticipants as $drawnParticipant) {
            // Create a new possible solution with the participant drawn
            $newCombination = $combination + [$participantIdx => $drawnParticipant];

            $newHat = $hat->diff([$drawnParticipant]);

            // Further check if this solution is possible
            yield from $this->solveWithExclusions(
                participantsIdx: $participantsIdx,
                currentIdx: $currentIdx + 1,
                exclusions: $exclusions,
                hat: $newHat,
                combination: $newCombination,
            );
        }
    }
}
