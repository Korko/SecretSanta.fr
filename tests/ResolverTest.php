<?php

use Korko\SecretSanta\Libs\Resolver;

class ResolverTest extends TestCase
{
    public function testDuoResolve()
    {
        $this->assertEquals([0 => 'B', 1 => 'A'], Resolver::resolve([
            0 => ['name' => 'A', 'exclusions' => []],
            1 => ['name' => 'B', 'exclusions' => []],
        ]));

        $this->assertEquals([0 => 'A', 1 => 'B'], Resolver::resolve([
            0 => ['name' => 'B', 'exclusions' => []],
            1 => ['name' => 'A', 'exclusions' => []],
        ]));
    }

    public function testTrioResolve()
    {
        $this->assertEquals([0 => 'B', 1 => 'C', 2 => 'A'], Resolver::resolve([
            0 => ['name' => 'A', 'exclusions' => []],
            1 => ['name' => 'B', 'exclusions' => ['0']],
            2 => ['name' => 'C', 'exclusions' => ['1']],
        ]));

        $this->assertEquals([0 => 'C', 1 => 'A', 2 => 'B'], Resolver::resolve([
            0 => ['name' => 'A', 'exclusions' => ['1']],
            1 => ['name' => 'B', 'exclusions' => ['2']],
            2 => ['name' => 'C', 'exclusions' => []],
        ]));
    }

    /**
     * @expectedException              Exception
     * @expectedExceptionMessageRegExp #^Cannot resolve#
     */
    public function testFailResolve()
    {
        Resolver::resolve([
            0 => ['name' => 'A', 'exclusions' => ['1', '2']],
            1 => ['name' => 'B', 'exclusions' => []],
            2 => ['name' => 'C', 'exclusions' => []],
        ]);
    }
}
