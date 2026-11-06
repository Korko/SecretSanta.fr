<?php

declare(strict_types=1);

namespace App\Solvers;

use Generator;

interface SolverInterface
{
    public function one(array $participants, array $exclusions = []): array;

    public function all(array $participants, array $exclusions = []): Generator;
}
