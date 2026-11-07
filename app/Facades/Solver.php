<?php

declare(strict_types=1);

namespace App\Facades;

use App\Solvers\SolverInterface;
use Generator;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array<int, int> one(array<int, mixed> $participants, array<int, list<int>> $exclusions = [])
 * @method static Generator<int, array<int, int>, mixed, void> all(array<int, mixed> $participants, array<int, list<int>> $exclusions = [])
 *
 * @see SolverInterface
 */
final class Solver extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return SolverInterface::class;
    }
}
