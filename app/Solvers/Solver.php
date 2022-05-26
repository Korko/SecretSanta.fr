<?php

namespace App\Solvers;

use App\Exceptions\SolverException;
use Generator;
use Illuminate\Support\Collection;

abstract class Solver implements SolverInterface
{
    public function one(Collection|array $participants, Collection|array|null $exclusions = null) : array
    {
        $generator = $this->all($participants, $exclusions);
        if (! $generator->valid()) {
            throw new SolverException('Cannot solve');
        }

        return $generator->current();
    }

    public function all(Collection|array $participants, Collection|array|null $exclusions = null) : Generator
    {
        $participants = is_array($participants) ? collect($participants) : $participants;
        $exclusions = is_null($exclusions) ? collect() : (is_array($exclusions) ? collect($exclusions) : $exclusions);

        if(count($participants) < 2) {
            throw new SolverException('Not enough participants');
        }

        // We ignore the participants details, we just want the ids
        return $this->solve($participants->keys(), $exclusions);
    }

    abstract protected function solve(Collection $participantsIdx, Collection $exclusions) : Generator;
}
