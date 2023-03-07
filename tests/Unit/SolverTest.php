<?php

namespace Tests\Unit;

use App\Exceptions\SolverException;
use App\Solvers\GraphSolver;
use App\Solvers\HatSolver;
use App\Solvers\Solver;
use Tests\TestCase;

class SolverTest extends TestCase
{
    /**
     * Solver provider.
     *
     * @return \App\Solvers\Solver[][]
     */
    public function solverProvider(): array
    {
        return [
            HatSolver::class => [new HatSolver],
            GraphSolver::class => [new GraphSolver],
        ];
    }

    const LOOP_NUMBER = 100;

    public function assertCombination($expected, $combinations, $participants): void
    {
        $actual = [];
        foreach ($combinations as $combination) {
            ksort($combination);
            array_walk($combination, function (&$idx) use ($participants) {
                $idx = $participants[$idx];
            });

            $actual[] = implode('', $combination);
        }

        sort($actual);
        sort($expected);

        $this->assertEquals(serialize($expected), serialize($actual));
    }

    /**
     * Test if the solver can found solutions even if the participants are separated in small groups
     *
     * @dataProvider solverProvider
     */
    public function testSplitGraph(Solver $solver): void
    {
        $participants = ['A', 'B', 'C', 'D'];
        $this->assertCombination([
            'BCDA', 'BDAC',
            'CADB', 'CDBA',
            'DABC', 'DCAB',
            // Those contains separate groups
            'BADC', // A => B, B => A // C => D, D => C
            'CDAB', // A => C, C => A // B => D, D => B
            'DCBA', // A => D, D => A // B => C, C => B
        ], $solver->all($participants), $participants);

        $participants = ['A', 'B', 'C', 'D', 'E', 'F'];
        $this->assertCombination([
            // ABC // DEF
            'BCA'.'EFD', 'CAB'.'EFD',
            'BCA'.'FDE', 'CAB'.'FDE',
        ], $solver->all($participants, [
            0 => [3, 4, 5],
            1 => [3, 4, 5],
            2 => [3, 4, 5],
            3 => [0, 1, 2],
            4 => [0, 1, 2],
            5 => [0, 1, 2],
        ]), $participants);

        $participants = ['A', 'B', 'C', 'D', 'E', 'F'];
        $this->assertCombination([
            // AB // CD // EF
            'BA'.'DC'.'FE',
        ], $solver->all($participants, [
            0 => [2, 3, 4, 5],
            1 => [2, 3, 4, 5],
            2 => [0, 1, 4, 5],
            3 => [0, 1, 4, 5],
            4 => [0, 1, 2, 3],
            5 => [0, 1, 2, 3],
        ]), $participants);
    }

    /**
     * Test with several participants without exclusions to see if all combinaisons are listed
     *
     * @dataProvider solverProvider
     */
    public function testNoExclusion(Solver $solver): void
    {
        $participants = ['A', 'B'];
        $this->assertCombination([
            'BA',
        ], $solver->all($participants), $participants);

        $participants = ['A', 'B', 'C'];
        $this->assertCombination([
            'BCA', 'CAB',
        ], $solver->all($participants), $participants);
    }

    /**
     * Test if a single exclusion is correctly handled
     *
     * @dataProvider solverProvider
     */
    public function testSimpleExclusion(Solver $solver): void
    {
        // A => C
        $participants = ['A', 'B', 'C'];
        $this->assertCombination([
            'BCA',
        ], $solver->all($participants, [0 => [2]]), $participants);
    }

    /**
     * Test if an impossible solution due to exclusions is correctly handled
     *
     * @dataProvider solverProvider
     */
    public function testImpossibleSolution(Solver $solver): void
    {
        $participants = ['A', 'B', 'C'];
        $this->assertCombination([], $solver->all($participants, [0 => [1, 2]]), $participants);
        $this->assertCombination([], $solver->all($participants, [2 => [0, 1]]), $participants);
    }

    /**
     * Test if we can find 2 differents solutions by random in a least LOOP_NUMBER iterations
     *
     * @dataProvider solverProvider
     */
    public function testOne(Solver $solver): void
    {
        $this->assertTrue((function () use ($solver) {
            $solutions = [
                [0 => 1, 1 => 2, 2 => 0],
                [0 => 2, 1 => 0, 2 => 1],
            ];
            $valid = [];

            for ($i = 0; $i < self::LOOP_NUMBER; $i++) {
                $solution = $solver->one(['A', 'B', 'C']);

                $solutionIdx = array_search($solution, $solutions);
                $valid[$solutionIdx] = true;

                if (count($valid) === count($solutions)) {
                    return true;
                }
            }

            return false;
        })());
    }

    /**
     * Test if the system can handle a large amount of participants in a single call
     *
     * @dataProvider solverProvider
     */
    public function testMass(Solver $solver): void
    {
        // 702 characters from 'A' to 'ZZ'
        $participants = [];
        for ($n = 'A'; $n !== 'AAA'; $n++) {
            $participants[] = $n;
        }

        try {
            $this->assertNotEquals(null, $solver->one($participants));
        } catch (SolverException) {
            $this->fail('No exception expected');
        }
    }

    /**
     * Test if the system is random enough
     *
     * @dataProvider solverProvider
     * @large
     */
    public function testRandom(Solver $solver): void
    {
        // 26 characters from 'A' to 'Z'
        $participants = range('A', 'Z');

        // Let's run the algo N times
        $solutions = [];
        for ($i = 0; $i < 10000; $i++) {
            try {
                $solution = $solver->one($participants);
                ksort($solution, SORT_NUMERIC);
                $solutions[] = json_encode($solution);
            } catch (SolverException) {
                $this->fail('No exception expected');
            }
        }

        // And now count how many times we had each solution
        $solutionsAndCount = [];
        foreach ($solutions as $solution) {
            if (! isset($solutionsAndCount[$solution])) {
                $solutionsAndCount[$solution] = 0;
                foreach ($solutions as $solution2) {
                    if ($solution === $solution2) {
                        $solutionsAndCount[$solution]++;
                    }
                }
            }
        }

        $this->assertTrue(count($solutionsAndCount) === count($solutions)); // We accept 1 redudency in solutions
    }

    /**
     * Test if a lot of exclusions is correctly handled
     *
     * @dataProvider solverProvider
     * @large
     */
    public function testSeveralExclusions(Solver $solver): void
    {
        $participants = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O'];
        try {
            $this->assertNotEquals(null, $solver->one($participants, [0 => [7, 10, 4, 13], 1 => [10], 6 => [11, 10], 7 => [0], 8 => [9, 10, 7], 10 => [0, 6], 11 => [6], 14 => [0, 1, 2, 4, 5, 6, 8, 6, 9, 10, 11, 12, 13]]));
        } catch (SolverException) {
            $this->fail('No exception expected');
        }
    }
}
