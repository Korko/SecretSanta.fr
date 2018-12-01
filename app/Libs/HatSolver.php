<?php

namespace App\Libs;

use App\Exceptions\SolverException;
use Generator;

class HatSolver
{
    public function one(array $participants, array $exclusions = []) : array
    {
        $hat = array_keys($participants);
        shuffle($hat);

        $generator = $this->getGenerator($participants, $exclusions, $hat);
        if (!$generator->valid()) {
            throw new SolverException('Cannot solve');
        }

        return $generator->current();
    }

    public function all(array $participants, array $exclusions = []) : array
    {
        $combinations = [];

        $generator = $this->getGenerator($participants, $exclusions, array_keys($participants));
        foreach ($generator as $combination) {
            $combinations[] = $combination;
        }

        return $combinations;
    }

    private function getGenerator(array $participants, array $exclusions, array $hat) : Generator
    {
        return $this->solve(0, [], $exclusions, $hat);
    }

    private function solve($i, array $combination, array $exclusions, array $hat) : Generator
    {
        $actualExclusions = array_key_exists($i, $exclusions) ? $exclusions[$i] : [];
        $actualHat = array_diff($hat, $actualExclusions, [$i]);

        if ($actualHat !== []) {
            foreach ($actualHat as $possibility) {
                $possibilityCombination = $combination + [$i => $possibility];
                $possibilityHat = array_diff($hat, [$possibility]);

                if ($possibilityHat === []) {
                    yield $possibilityCombination;
                } else {
                    yield from $this->solve($i + 1, $possibilityCombination, $exclusions, $possibilityHat);
                }
            }
        } else {
            return;
        }
    }
}
