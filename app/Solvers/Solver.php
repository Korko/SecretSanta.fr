<?php

namespace App\Solvers;

use Illuminate\Support\Facades\Facade;

class Solver extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return SolverInterface::class; }
}