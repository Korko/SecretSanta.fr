<?php

namespace App\Solvers;

use Generator;
use Illuminate\Support\Collection;

interface SolverInterface
{
    public function one(Collection|array $participantsIdx, Collection|array|null $exclusions = null) : array;
    public function all(Collection|array $participantsIdx, Collection|array|null $exclusions = null) : Generator;
}