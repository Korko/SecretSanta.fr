<?php

namespace App\Services;

use App\Exceptions\SolverException;
use Generator;

class HatSolver
{
    public function one(array $participants, array $exclusions = []) : array
    {
        $hat = array_keys($participants);
        shuffle($hat);

        $generator = $this->yieldCombinations($exclusions, $hat);
        if (! $generator->valid()) {
            throw new SolverException('Cannot solve');
        }

        return $generator->current();
    }

    public function all(array $participants, array $exclusions = []) : Generator
    {
        return $this->yieldCombinations($exclusions, array_keys($participants));
    }

    private function yieldCombinations(array $exclusions, array $hat) : Generator
    {
        return $this->solve(0, [], $exclusions, $hat);
    }

    private function solve(int $participantIdx, array $combination, array $exclusions, array $hat) : Generator
    {
        // End of a loop, we've found a possible combination
        if ($hat === []) {
            yield $combination;
        }

        $actualExclusions = array_key_exists($participantIdx, $exclusions) ? $exclusions[$participantIdx] : [];
        $actualHat = array_diff($hat, $actualExclusions, [$participantIdx]);

        foreach ($actualHat as $possibleParticipant) {
            $possibilityCombination = $combination + [$participantIdx => $possibleParticipant];
            $possibilityHat = array_diff($hat, [$possibleParticipant]);

            yield from $this->solve($participantIdx + 1, $possibilityCombination, $exclusions, $possibilityHat);
        }
    }
}
