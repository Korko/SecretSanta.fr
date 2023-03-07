<?php

namespace App\Facades;

use App\Solvers\SolverInterface;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array one(array $participants, array $exclusions = [])
 * @method static Generator all(array $participants, array $exclusions = [])
 *
 * @see \App\Solvers\SolverInterface
 */
class Solver extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return SolverInterface::class;
    }
}
