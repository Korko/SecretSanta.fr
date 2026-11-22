<?php

declare(strict_types=1);

namespace App\Solvers;

use App\Exceptions\SolverException;

interface SolverInterface
{
    /**
     * @param  array<int, mixed>  $participants  indexed by participant id
     * @param  array<int, list<int>>  $exclusions  participant id => excluded ids
     * @return array<int, int> santa id => target id
     *
     * @throws SolverException when no draw is possible
     */
    public function one(array $participants, array $exclusions = []): array;

    /**
     * Whether a draw is possible, without making one
     *
     * @param  array<int, mixed>  $participants  indexed by participant id
     * @param  array<int, list<int>>  $exclusions  participant id => excluded ids
     */
    public function solvable(array $participants, array $exclusions = []): bool;
}
