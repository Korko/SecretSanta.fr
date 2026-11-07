<?php

declare(strict_types=1);

namespace App\Solvers;

use App\Exceptions\SolverException;
use Generator;
use Random\Randomizer;

final class HatSolver implements SolverInterface
{
    /**
     * @param  array<int, mixed>  $participants  indexed by participant id
     * @param  array<int, list<int>>  $exclusions  participant id => excluded ids
     * @return array<int, int> santa id => target id
     */
    public function one(array $participants, array $exclusions = []): array
    {
        $generator = $this->all($participants, $exclusions);
        throw_unless($generator->valid(), SolverException::class, 'Cannot solve');

        return $generator->current();
    }

    /**
     * @param  array<int, mixed>  $participants  indexed by participant id
     * @param  array<int, list<int>>  $exclusions  participant id => excluded ids
     * @return Generator<int, array<int, int>, mixed, void>
     */
    public function all(array $participants, array $exclusions = []): Generator
    {
        throw_if(count($participants) < 2, SolverException::class, 'Not enough participants');

        $hat = array_keys($participants);

        // Obvious dead ends (someone with nobody to draw, or that nobody can draw) would only be found
        // after exploring every combination, which grows factorially with the number of participants
        foreach ($hat as $id) {
            $canDraw = array_diff($hat, [$id], $exclusions[$id] ?? []) !== [];
            $canBeDrawn = array_any($hat, fn (int $santa): bool => $santa !== $id && ! in_array($id, $exclusions[$santa] ?? [], true));
            if (! $canDraw || ! $canBeDrawn) {
                return;
            }
        }

        $hat = (new Randomizer)->shuffleArray($hat);

        uksort($participants, function (int $participantIdx1, int $participantIdx2) use ($exclusions): int {
            $count1 = count($exclusions[$participantIdx1] ?? []);
            $count2 = count($exclusions[$participantIdx2] ?? []);

            return $count1 > $count2 ? -1 : (
                $count1 === $count2 ? 0 : 1
            );
        });

        yield from $this->solve(array_keys($participants), 0, [], $exclusions, $hat);
    }

    /**
     * @param  list<int>  $participantIds
     * @param  array<int, int>  $combination
     * @param  array<int, list<int>>  $allExclusions
     * @param  array<int, int>  $currentHat
     * @return Generator<int, array<int, int>, mixed, void>
     */
    private function solve(array $participantIds, int $idx, array $combination, array $allExclusions, array $currentHat): Generator
    {
        // End of a loop, we've found a possible combination
        if ($currentHat === []) {
            ksort($combination);
            yield $combination;

            return;
        }

        $participantIdx = $participantIds[$idx];

        // Get the exclusions requested for that participant
        // And remove them from the hat (+ the current participant)
        $exclusions = $allExclusions[$participantIdx] ?? [];
        $hat = array_diff($currentHat, $exclusions, [$participantIdx]);

        // If nothing available in the hat for that participant
        // Then nothing will happen and the combination will be lost
        foreach ($hat as $drawnParticipant) {
            // Create a new possible solution with the participant drawn
            $newCombination = $combination + [$participantIdx => $drawnParticipant];
            $newHat = array_diff($currentHat, [$drawnParticipant]);

            // Further check if this solution is possible
            yield from $this->solve($participantIds, $idx + 1, $newCombination, $allExclusions, $newHat);
        }
    }
}
