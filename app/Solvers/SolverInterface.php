<?php

declare(strict_types=1);

namespace App\Solvers;

use Generator;

interface SolverInterface
{
    /**
     * @param  array<int, mixed>  $participants  indexed by participant id
     * @param  array<int, list<int>>  $exclusions  participant id => excluded ids
     * @return array<int, int> santa id => target id
     */
    public function one(array $participants, array $exclusions = []): array;

    /**
     * @param  array<int, mixed>  $participants  indexed by participant id
     * @param  array<int, list<int>>  $exclusions  participant id => excluded ids
     * @return Generator<int, array<int, int>, mixed, void>
     */
    public function all(array $participants, array $exclusions = []): Generator;
}
