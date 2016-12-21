<?php

class SolverTest extends TestCase
{
    public function assertCombination($valid, $test, $participants)
    {
        $test = array_map(function ($el) use ($participants) {
            array_walk($el, function (&$idx) use ($participants) {
                $idx = $participants[$idx];
            });

            return implode('', $el);
        }, $test);
        $this->assertEquals(sort($valid), sort($test));
    }

    public function testNoExclusion()
    {
        $this->assertCombination([
            'BA',
        ], Solver::all(['A', 'B']), ['A', 'B']);

        $this->assertCombination([
            'BCA', 'CAB',
        ], Solver::all(['A', 'B', 'C']), ['A', 'B', 'C']);

        $this->assertCombination([
            'BCDA', 'BDAC', 'BADC',
            'CDAB', 'CADB', 'CDBA',
            'DABC', 'DCBA', 'DCAB',
        ], Solver::all(['A', 'B', 'C', 'D']), ['A', 'B', 'C', 'D']);
    }

    public function testSimpleExclusion()
    {
        // A => C
        $this->assertCombination([
            'BCA',
        ], Solver::all(['A', 'B', 'C'], [0 => [2]]), ['A', 'B', 'C']);
    }

/*
    public function testComplexExclusion()
    {
        $participants = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', L'];
        $exclusions = [0 => [1, 2, 3]];

        $this->assertEquals(array(['A' => 'B', 'B' => 'C', 'C' => 'A']), Solver::all($participants, function($elementA, $elementB) use($exclusions) {
            return !isset($exclusions[$elementA]) || !in_array($elementB, $exclusions[$elementA]);
        }));
    }
*/

    public function testImpossibleSolution()
    {
        $this->assertEquals([], Solver::all(['A', 'B', 'C'], [0 => [1, 2]]), ['A', 'B', 'C']);
    }

    public function testOne()
    {
        srand(1);
        $rand1 = rand(0, 10);
        srand(1);
        $rand2 = rand(0, 10);
        if ($rand1 !== $rand2) {
            $this->markTestSkipped('srand disabled');

            return;
        }

        srand(1);
        $this->assertEquals([0 => 1, 1 => 2, 2 => 0], Solver::one(['A', 'B', 'C']));

        srand(123);
        $this->assertEquals([0 => 2, 1 => 0, 2 => 1], Solver::one(['A', 'B', 'C']));
    }

    public function testMass()
    {
        // 702 characters from 'A' to 'ZZ'
        $participants = [];
        for ($n = 'A'; $n !== 'AAA'; $n++) {
            $participants[] = $n;
        }

        Solver::one($participants);
    }
}
