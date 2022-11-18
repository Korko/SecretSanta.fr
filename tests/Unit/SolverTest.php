<?php

namespace Tests\Unit;

use App\Exceptions\SolverException;
use Solver;
use Tests\TestCase;

class SolverTest extends TestCase
{
    const LOOP_NUMBER = 100;

    public function assertCombination($expected, $combinations, $participants): void
    {
        $actual = [];
        foreach ($combinations as $combination) {
            array_walk($combination, function (&$idx) use ($participants) {
                $idx = $participants[$idx];
            });

            $actual[] = implode('', $combination);
        }

        sort($actual);
        sort($expected);

        $this->assertEquals(serialize($actual), serialize($expected));
    }

    public function testNoExclusion(): void
    {
        $participants = ['A', 'B'];
        $this->assertCombination([
            'BA',
        ], Solver::all($participants), $participants);

        $participants = ['A', 'B', 'C'];
        $this->assertCombination([
            'BCA', 'CAB',
        ], Solver::all($participants), $participants);

        $participants = ['A', 'B', 'C', 'D'];
        $this->assertCombination([
            'BCDA', 'BDAC', 'BADC',
            'CDAB', 'CADB', 'CDBA',
            'DABC', 'DCBA', 'DCAB',
        ], Solver::all($participants), $participants);
    }

    public function testSimpleExclusion(): void
    {
        // A => C
        $participants = ['A', 'B', 'C'];
        $this->assertCombination([
            'BCA',
        ], Solver::all($participants, [0 => [2]]), $participants);
    }

    public function testImpossibleSolution(): void
    {
        $participants = ['A', 'B', 'C'];
        $this->assertCombination([], Solver::all($participants, [0 => [1, 2]]), $participants);
        $this->assertCombination([], Solver::all($participants, [2 => [0, 1]]), $participants);
    }

    public function testOne(): void
    {
        $this->assertTrue((function () {
            $solutions = [
                [0 => 1, 1 => 2, 2 => 0],
                [0 => 2, 1 => 0, 2 => 1],
            ];
            $valid = [];

            for ($i = 0; $i < self::LOOP_NUMBER; $i++) {
                $solution = Solver::one(['A', 'B', 'C']);

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
     * @doesNotPerformAssertions
     */
    public function testMass(): void
    {
        // 702 characters from 'A' to 'ZZ'
        $participants = [];
        for ($n = 'A'; $n !== 'AAA'; $n++) {
            $participants[] = $n;
        }

        try {
            Solver::one($participants);
        } catch (SolverException $e) {
            $this->fail('No exception expected');
        }
    }

    /**
     * Test if the system is random enough
     */
    public function testRandom(): void
    {
        // 26 characters from 'A' to 'Z'
        $participants = range('A', 'Z');

        // Let's run the algo N times
        $solutions = [];
        for($i = 0; $i < 10000; $i++) {
            try {
                $solution = Solver::one($participants);
                ksort($solution, SORT_NUMERIC);
                $solutions[] = json_encode($solution);
            } catch (SolverException) {
                $this->fail('No exception expected');
            }
        }

        // And now count how many times we had each solution
        $solutionsAndCount = [];
        foreach($solutions as $solution) {
            if(!isset($solutionsAndCount[$solution])) {
                $solutionsAndCount[$solution] = 0;
                foreach($solutions as $solution2) {
                    if($solution === $solution2) {
                        $solutionsAndCount[$solution]++;
                    }
                }
            }
        }

        $this->assertTrue(count($solutionsAndCount) === count($solutions)); // We accept 1 redudency in solutions
    }
}
