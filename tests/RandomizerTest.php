<?php

use Korko\SecretSanta\Libs\Randomizer;

class RandomizerTest extends TestCase
{
    public function testDuoRandomize()
    {
        $this->assertEquals([0 => 'B', 1 => 'A'], Randomizer::randomize([
            0 => ['name' => 'A', 'exclusions' => []],
            1 => ['name' => 'B', 'exclusions' => []],
        ]));

        $this->assertEquals([0 => 'A', 1 => 'B'], Randomizer::randomize([
            0 => ['name' => 'B', 'exclusions' => []],
            1 => ['name' => 'A', 'exclusions' => []],
        ]));
    }

    public function testTrioRandomize()
    {
        $this->assertEquals([0 => 'B', 1 => 'C', 2 => 'A'], Randomizer::randomize([
            0 => ['name' => 'A', 'exclusions' => []],
            1 => ['name' => 'B', 'exclusions' => ['0']],
            2 => ['name' => 'C', 'exclusions' => ['1']],
        ]));

        $this->assertEquals([0 => 'C', 1 => 'A', 2 => 'B'], Randomizer::randomize([
            0 => ['name' => 'A', 'exclusions' => ['1']],
            1 => ['name' => 'B', 'exclusions' => ['2']],
            2 => ['name' => 'C', 'exclusions' => []],
        ]));
    }

    /**
     * @expectedException              Exception
     * @expectedExceptionMessageRegExp #^Cannot resolve#
     */
    public function testFailRandomize()
    {
        Randomizer::randomize([
            0 => ['name' => 'A', 'exclusions' => ['1', '2']],
            1 => ['name' => 'B', 'exclusions' => []],
            2 => ['name' => 'C', 'exclusions' => []],
        ]);
    }
}
