<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Exceptions\SolverException;
use App\Solvers\CycleSolver;
use Random\Engine\Mt19937;
use Random\Randomizer;
use Solver;
use Tests\TestCase;

final class CycleSolverTest extends TestCase
{
    private CycleSolver $solver;

    protected function setUp(): void
    {
        parent::setUp();

        // Seeded: the draws, and so the statistics below, are the same on every run
        $this->solver = new CycleSolver(new Randomizer(new Mt19937(2026)));
    }

    public function test_two_participants_draw_each_other(): void
    {
        $this->assertSame([0 => 1, 1 => 0], $this->solver->one(['A', 'B']));
    }

    public function test_the_registered_solver_draws(): void
    {
        $this->assertDraw(Solver::one(['A', 'B', 'C']), 3);
        $this->assertTrue(Solver::solvable(['A', 'B', 'C']));
    }

    public function test_draws_a_single_loop(): void
    {
        $seen = [];
        for ($i = 0; $i < 100; $i++) {
            $seen[implode('', $this->solver->one(['A', 'B', 'C']))] = true;
        }

        ksort($seen);
        $this->assertSame(['120', '201'], array_map(strval(...), array_keys($seen)));

        for ($i = 0; $i < 200; $i++) {
            $draw = $this->solver->one(range(0, 9));

            $this->assertDraw($draw, 10);
            $this->assertSame(1, $this->loops($draw));
        }
    }

    public function test_respects_exclusions(): void
    {
        // A cannot draw C: A -> B -> C -> A is the only draw left
        for ($i = 0; $i < 20; $i++) {
            $this->assertSame([0 => 1, 1 => 2, 2 => 0], $this->solver->one(['A', 'B', 'C'], [0 => [2]]));
        }

        // Five couples who don't draw each other
        $couples = array_map(fn (int $id): array => [$id ^ 1], range(0, 9));
        for ($i = 0; $i < 200; $i++) {
            $draw = $this->solver->one(range(0, 9), $couples);

            $this->assertDraw($draw, 10, $couples);
            $this->assertSame(1, $this->loops($draw));
        }
    }

    public function test_keeps_the_participant_ids(): void
    {
        $draw = $this->solver->one([57 => 'A', 3 => 'B', 12 => 'C', 40 => 'D'], [3 => [12]]);

        $this->assertSame([3, 12, 40, 57], array_keys($draw));
        $this->assertEqualsCanonicalizing([3, 12, 40, 57], array_values($draw));
        $this->assertNotSame(12, $draw[3]);
        $this->assertSame(1, $this->loops($draw));
    }

    /**
     * No draw is more likely than another, whatever the order of the participants in the form
     */
    public function test_every_loop_is_as_likely(): void
    {
        $draws = [];
        $pairs = [];
        for ($i = 0; $i < 24000; $i++) {
            $draw = $this->solver->one(range(0, 4));

            $key = implode('', $draw);
            $draws[$key] = ($draws[$key] ?? 0) + 1;
            foreach ($draw as $santa => $target) {
                $pairs[$santa.$target] = ($pairs[$santa.$target] ?? 0) + 1;
            }
        }

        // The 4! loops of 5 participants, 1000 times each on average
        $this->assertCount(24, $draws);
        $this->assertGreaterThan(880, min($draws));
        $this->assertLessThan(1120, max($draws));

        // Each santa draws each of the 4 others a quarter of the time
        $this->assertCount(20, $pairs);
        $this->assertGreaterThan(5700, min($pairs));
        $this->assertLessThan(6300, max($pairs));
    }

    public function test_random(): void
    {
        $draws = [];
        for ($i = 0; $i < 1000; $i++) {
            $draws[implode(',', $this->solver->one(range('A', 'Z')))] = true;
        }

        $this->assertCount(1000, $draws);
    }

    public function test_impossible_solution(): void
    {
        // A cannot draw anyone, nobody can draw C
        $this->assertUnsolvable(['A', 'B', 'C'], [0 => [1, 2]]);
        $this->assertUnsolvable(['A', 'B', 'C'], [0 => [2], 1 => [2]]);
        $this->assertUnsolvable(['A']);
    }

    /**
     * Everyone can draw someone and be drawn by someone, yet 0 and 1 can only be drawn by 2:
     * trying every combination would take hours from 15 participants on
     */
    public function test_impossible_solution_is_found_quickly(): void
    {
        $exclusions = array_map(fn (int $id): array => $id === 2 ? [] : [0, 1], range(0, 49));

        $start = microtime(true);
        $this->assertUnsolvable(range(0, 49), $exclusions);
        $this->assertLessThan(1, microtime(true) - $start);
    }

    /**
     * Two teams drawing only in the other one: too few circles fit for the shuffles to find one
     */
    public function test_dense_exclusions_still_give_a_single_loop(): void
    {
        $teams = array_map(fn (int $id): array => $id < 10 ? range(0, 9) : range(10, 19), range(0, 19));

        for ($i = 0; $i < 20; $i++) {
            $draw = $this->solver->one(range(0, 19), $teams);

            $this->assertDraw($draw, 20, $teams);
            $this->assertSame(1, $this->loops($draw));
        }
    }

    public function test_several_loops_when_the_exclusions_leave_no_single_one(): void
    {
        // A and B can only draw each other, and so do C and D
        $exclusions = [0 => [2, 3], 1 => [2, 3], 2 => [0, 1], 3 => [0, 1]];

        $this->assertTrue($this->solver->solvable(range(0, 3), $exclusions));
        $this->assertSame([0 => 1, 1 => 0, 2 => 3, 3 => 2], $this->solver->one(range(0, 3), $exclusions));
    }

    public function test_mass(): void
    {
        // 702 participants, from 'A' to 'ZZ'
        $participants = [];
        for ($n = 'A'; $n !== 'AAA'; $n++) {
            $participants[] = $n;
        }

        $draw = $this->solver->one($participants);

        $this->assertDraw($draw, 702);
        $this->assertSame(1, $this->loops($draw));
        $this->assertTrue($this->solver->solvable($participants));
    }

    /**
     * Test if a lot of exclusions is correctly handled
     */
    public function test_several_exclusions(): void
    {
        $exclusions = [0 => [7, 10, 4, 13], 1 => [10], 6 => [11, 10], 7 => [0], 8 => [9, 10, 7], 10 => [0, 6], 11 => [6], 14 => [0, 1, 2, 4, 5, 6, 8, 6, 9, 10, 11, 12, 13]];

        for ($i = 0; $i < 50; $i++) {
            $draw = $this->solver->one(range(0, 14), $exclusions);

            $this->assertDraw($draw, 15, $exclusions);
            $this->assertSame(1, $this->loops($draw));
        }
    }

    /**
     * @param  array<int, int>  $draw
     * @param  array<int, list<int>>  $exclusions
     */
    private function assertDraw(array $draw, int $count, array $exclusions = []): void
    {
        $this->assertSame(range(0, $count - 1), array_keys($draw));
        $this->assertEqualsCanonicalizing(range(0, $count - 1), array_values($draw));

        foreach ($draw as $santa => $target) {
            $this->assertNotSame($santa, $target);
            $this->assertNotContains($target, $exclusions[$santa] ?? []);
        }
    }

    /**
     * @param  array<int, string|int>  $participants
     * @param  array<int, list<int>>  $exclusions
     */
    private function assertUnsolvable(array $participants, array $exclusions = []): void
    {
        $this->assertFalse($this->solver->solvable($participants, $exclusions));

        try {
            $this->solver->one($participants, $exclusions);
            $this->fail('A draw was made');
        } catch (SolverException) {
            // Expected
        }
    }

    /**
     * @param  array<int, int>  $draw
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
