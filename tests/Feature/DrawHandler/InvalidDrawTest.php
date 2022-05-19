<?php

use App\Exceptions\SolverException;
use App\Models\Draw;
use App\Models\Participant;
use App\Services\DrawFormHandler;

it('throws an exception when there\'s no solution', function ($participants) {
    createServiceDraw($participants);
})->with('invalid participants list')->throws(SolverException::class);

it('does not record anything in case of error', function ($participants) {
    assertModelCount(Draw::class, 0);
    assertModelCount(Participant::class, 0);

    try { createServiceDraw($participants); } catch (Exception $e) {}

    assertModelCount(Draw::class, 0);
    assertModelCount(Participant::class, 0);
})->with('invalid participants list');
