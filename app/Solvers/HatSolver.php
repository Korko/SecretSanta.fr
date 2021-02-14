<?php

namespace App\Solvers;

use App\Exceptions\SolverException;
use Arr;
use Generator;

class HatSolver implements SolverInterface
{
    public function one(array $participants, array $exclusions = []) : array
    {
        $generator = $this->all($participants, $exclusions);
        if (! $generator->valid()) {
            throw new SolverException('Cannot solve');
        }

        return $generator->current();
    }

    public function all(array $participants, array $exclusions = []) : Generator
    {
        if(count($participants) < 2) {
            throw new SolverException('Not enough participants');
        }

        $hat = array_keys($participants);
        shuffle($hat);

        return $this->solve(array_keys($participants)[0], [], $exclusions, $hat);
    }

    private function solve(int $participantIdx, array $combination, array $allExclusions, array $currentHat) : Generator
    {
        // End of a loop, we've found a possible combination
        if ($currentHat === []) {
            yield $combination;
        }

        // Get the exclusions requested for that participant
        // And remove them from the hat (+ the current participant)
        $exclusions = Arr::get($allExclusions, $participantIdx, []);
        $hat = array_diff($currentHat, $exclusions, [$participantIdx]);

        // If nothing available in the hat for that participant
        // Then nothing will happen and the combination will be lost
        foreach ($hat as $drawnParticipant) {
            // Create a new possible solution with the participant drawn
            $newCombination = $combination + [$participantIdx => $drawnParticipant];
            $newHat = array_diff($currentHat, [$drawnParticipant]);

            // Further check if this solution is possible
            yield from $this->solve($participantIdx + 1, $newCombination, $allExclusions, $newHat);
        }
    }
}
