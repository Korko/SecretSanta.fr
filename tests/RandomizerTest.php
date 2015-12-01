<?php

use App\Libs\Randomizer;

class RandomizerTest extends TestCase
{
    public function testDuoRandomize()
    {
        $this->assertEquals([0 => 'B', 1 => 'A'], Randomizer::randomize([
		0 => ['name' => 'A', 'partner' => null],
		1 => ['name' => 'B', 'partner' => null]
	]));

        $this->assertEquals([0 => 'A', 1 => 'B'], Randomizer::randomize([
		0 => ['name' => 'B', 'partner' => null],
		1 => ['name' => 'A', 'partner' => null]
	]));
    }

    public function testTrioRandomize()
    {
        $this->assertEquals([0 => 'B', 1 => 'C', 2 => 'A'], Randomizer::randomize([
                0 => ['name' => 'A', 'partner' => null],
                1 => ['name' => 'B', 'partner' => 'A'],
		2 => ['name' => 'C', 'partner' => 'B']
        ]));

        $this->assertEquals([0 => 'C', 1 => 'A', 2 => 'B'], Randomizer::randomize([
                0 => ['name' => 'A', 'partner' => 'B'],
                1 => ['name' => 'B', 'partner' => 'C'],
                2 => ['name' => 'C', 'partner' => null]
        ]));
    }
}
