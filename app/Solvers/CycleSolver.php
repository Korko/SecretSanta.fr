<?php

declare(strict_types=1);

namespace App\Solvers;

use App\Exceptions\SolverException;
use Random\Randomizer;

/**
 * Draws who gives to whom, as a single loop whenever it finds one (A -> B -> C -> A): nobody draws themselves
 * or someone they excluded. From three participants on, a single loop means no two people draw each other, but
 * dense exclusions can leave several loops, pairs included (A can only give to B and B only to A), and so can the
 * exchange of targets done by ParticipantsEditor when an exclusion blocks a removal.
 *
 * 1. Everyone is shuffled into a circle and gives to the next one, again until no exclusion is hit: every
 *    possible loop is then as likely as any other. A few shuffles are enough unless exclusions are very dense.
 * 2. Otherwise a bipartite matching finds a valid draw, or tells there is none, in polynomial time.
 * 3. That draw is then shuffled by random exchanges of targets that never add a loop, so its loops merge
 *    when the exclusions allow it. Still random, but not every draw is exactly as likely as any other.
 */
final readonly class CycleSolver implements SolverInterface
{
    /** Circles tried before the matching: each one only costs a shuffle and a pass over the participants */
    private const int MAX_SHUFFLES = 2000;

    /** Upper bound of the work done by the exchanges of step 3, each one costing a pass over the participants */
    private const int MAX_WALK_WORK = 2_000_000;

    public function __construct(private Randomizer $randomizer = new Randomizer) {}

    public function one(array $participants, array $exclusions = []): array
    {
        $ids = array_keys($participants);
        throw_if(count($ids) < 2, SolverException::class, 'Not enough participants');

        $forbidden = $this->forbidden($ids, $exclusions);

        $draw = $this->shuffledLoop($ids, $forbidden)
            ?? $this->walk($this->matching($ids, $forbidden) ?? throw new SolverException('Cannot solve'), $forbidden);

        ksort($draw);

        return $draw;
    }

    public function solvable(array $participants, array $exclusions = []): bool
    {
        $ids = array_keys($participants);

        return count($ids) >= 2 && $this->matching($ids, $this->forbidden($ids, $exclusions)) !== null;
    }

    /**
     * @param  list<int>  $ids
     * @param  array<int, list<int>>  $exclusions
     * @return array<int, array<int, true>> santa id => the ids they cannot draw (themselves included)
     */
    private function forbidden(array $ids, array $exclusions): array
    {
        $forbidden = [];
        foreach ($ids as $id) {
            $forbidden[$id] = array_fill_keys([$id, ...($exclusions[$id] ?? [])], true);
        }

        return $forbidden;
    }

    /**
     * Rejection sampling: uniform over the valid single loops (each loop comes from as many circles as participants)
     *
     * @param  list<int>  $ids
     * @param  array<int, array<int, true>>  $forbidden
     * @return array<int, int>|null santa id => target id
     */
    private function shuffledLoop(array $ids, array $forbidden): ?array
    {
        $count = count($ids);

        for ($shuffle = 0; $shuffle < self::MAX_SHUFFLES; $shuffle++) {
            // Fisher-Yates one place at a time, dropped as soon as a santa cannot give to the next one:
            // the circles kept are the same as with a full shuffle, so each loop stays as likely
            $circle = $ids;
            for ($position = 0; $position < $count; $position++) {
                $picked = $this->randomizer->getInt($position, $count - 1);
                [$circle[$position], $circle[$picked]] = [$circle[$picked], $circle[$position]];

                if ($position > 0 && isset($forbidden[$circle[$position - 1]][$circle[$position]])) {
                    continue 2;
                }
            }

            if (isset($forbidden[$circle[$count - 1]][$circle[0]])) {
                continue;
            }

            $draw = [];
            foreach ($circle as $position => $santa) {
                $draw[$santa] = $circle[($position + 1) % $count];
            }

            return $draw;
        }

        return null;
    }

    /**
     * Maximum bipartite matching between santas and targets (augmenting paths), in a random order
     *
     * @param  list<int>  $ids
     * @param  array<int, array<int, true>>  $forbidden
     * @return array<int, int>|null santa id => target id, null when someone cannot be given a target
     */
    private function matching(array $ids, array $forbidden): ?array
    {
        $free = array_fill_keys($this->randomizer->shuffleArray($ids), true);
        $santas = [];

        foreach ($this->randomizer->shuffleArray($ids) as $santa) {
            $seen = [];
            if (! $this->assign($santa, $forbidden, $free, $santas, $seen)) {
                return null;
            }
        }

        return array_flip($santas);
    }

    /**
     * Gives a target to the santa: a free one, or the target of another santa who can be given another one
     *
     * @param  array<int, array<int, true>>  $forbidden
     * @param  array<int, true>  $free  targets nobody has yet
     * @param  array<int, int>  $santas  target id => santa id
     * @param  array<int, true>  $seen  targets already tried for this path
     */
    private function assign(int $santa, array $forbidden, array &$free, array &$santas, array &$seen): bool
    {
        foreach (array_keys($free) as $target) {
            if (! isset($forbidden[$santa][$target])) {
                unset($free[$target]);
                $santas[$target] = $santa;

                return true;
            }
        }

        foreach ($santas as $target => $other) {
            if (isset($forbidden[$santa][$target]) || isset($seen[$target])) {
                continue;
            }

            $seen[$target] = true;
            if ($this->assign($other, $forbidden, $free, $santas, $seen)) {
                $santas[$target] = $santa;

                return true;
            }
        }

        return false;
    }

    /**
     * Random walk over the valid draws: two or three santas exchange their targets, as long as nobody gets a
     * forbidden one and the draw doesn't end up with more loops. Loops can only merge, and once there is a
     * single one the walk stays among the single loops.
     *
     * @param  array<int, int>  $draw  santa id => target id
     * @param  array<int, array<int, true>>  $forbidden
     * @return array<int, int> santa id => target id
     */
    private function walk(array $draw, array $forbidden): array
    {
        $ids = array_keys($draw);
        $count = count($ids);
        $loops = $this->loops($draw);

        for ($step = min(4 * $count ** 2, intdiv(self::MAX_WALK_WORK, $count)); $step > 0; $step--) {
            // Two or three different santas, in a random order: each one gets the target of the next one
            $picked = [];
            $size = $this->randomizer->getInt(2, min(3, $count));
            while (count($picked) < $size) {
                $picked[$ids[$this->randomizer->getInt(0, $count - 1)]] = true;
            }

            $santas = array_keys($picked);

            $exchanged = $draw;
            foreach ($santas as $position => $santa) {
                $target = $draw[$santas[($position + 1) % $size]];
                if (isset($forbidden[$santa][$target])) {
                    continue 2;
                }

                $exchanged[$santa] = $target;
            }

            $exchangedLoops = $this->loops($exchanged);
            if ($exchangedLoops <= $loops) {
                $draw = $exchanged;
                $loops = $exchangedLoops;
            }
        }

        return $draw;
    }

    /**
     * @param  array<int, int>  $draw  santa id => target id
     */
    private function loops(array $draw): int
    {
        $loops = 0;
        $seen = [];

        foreach (array_keys($draw) as $santa) {
            if (isset($seen[$santa])) {
                continue;
            }

            $loops++;
            for ($current = $santa; ! isset($seen[$current]); $current = $draw[$current]) {
                $seen[$current] = true;
            }
        }

        return $loops;
    }
}
