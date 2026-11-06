<?php

declare(strict_types=1);

namespace App\Facades;

use App\Solvers\SolverInterface;
use Generator;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array one(array $participants, array $exclusions = [])
 * @method static Generator all(array $participants, array $exclusions = [])
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
