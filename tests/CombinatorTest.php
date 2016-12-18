<?php

use Korko\SecretSanta\Libs\Combinator;

class CombinatorTest extends TestCase
{
    public function assertCombination($valid, $test)
    {
        $test = array_map(function ($el) {
            return implode('', $el);
        }, $test);
        $this->assertEquals(sort($valid), sort($test));
    }

    public function testNoFilter()
    {
        $this->assertCombination([
            'AB', 'BA',
        ], Combinator::all(['A', 'B']));
    }

    public function testNoExclusion()
    {
        $this->assertCombination([
            'BA',
        ], Combinator::all(['A', 'B'], function ($a, $b) {
            return $a !== $b;
        }));

        $this->assertCombination([
            'BCA', 'CAB',
        ], Combinator::all(['A', 'B', 'C'], function ($a, $b) {
            return $a !== $b;
        }));

        $this->assertCombination([
            'BCDA', 'BDAC', 'BADC',
            'CDAB', 'CADB', 'CDBA',
            'DABC', 'DCBA', 'DCAB',
        ], Combinator::all(['A', 'B', 'C', 'D'], function ($a, $b) {
            return $a !== $b;
        }));
    }

    public function testSimpleExclusion()
    {
        $exclusions = ['A' => ['C']];

        $this->assertCombination([
            'BCA',
        ], Combinator::all(['A', 'B', 'C'], function ($elementA, $elementB) use ($exclusions) {
            return $elementA !== $elementB && (!isset($exclusions[$elementA]) || !in_array($elementB, $exclusions[$elementA]));
        }));
    }

/*
    public function testComplexExclusion()
    {
        $participants = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', L'];
        $exclusions = ['A' => ['B', 'C', 'D']];

        $this->assertEquals(array(['A' => 'B', 'B' => 'C', 'C' => 'A']), Combinator::all($participants, function($elementA, $elementB) use($exclusions) {
            return !isset($exclusions[$elementA]) || !in_array($elementB, $exclusions[$elementA]);
        }));
    }
*/
    public function testInvalidExclusion()
    {
        $this->assertEquals([], Combinator::all(['A', 'B', 'C'], function ($elementA, $elementB) {
            return false;
        }));
    }

    public function testImpossibleSolution()
    {
        $exclusions = ['A' => ['B', 'C']];

        $this->assertEquals([], Combinator::all(['A', 'B', 'C'], function ($elementA, $elementB) use ($exclusions) {
            return $elementA !== $elementB && (!isset($exclusions[$elementA]) || !in_array($elementB, $exclusions[$elementA]));
        }));
    }
}
