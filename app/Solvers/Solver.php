<?php

namespace App\Solvers;

use App\Exceptions\SolverException;
use Generator;

abstract class Solver implements SolverInterface
{
    public function one(array $participants, array $exclusions = []) : array
    {
        $generator = $this->all($participants, $exclusions);
        if (! $generator->valid()) {
            throw new SolverException('Cannot solve');
        }

        return $generator->current();
    }

    public function all(array $participants, array $exclusions = []) : Generator
    {
        if(count($participants) < 2) {
            throw new SolverException('Not enough participants');
        }

        return $this->solve($participants, $exclusions);
    }

    abstract protected function solve(array $participants, array $exclusions = []) : Generator;
}