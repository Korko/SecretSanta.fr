<?php

use App\Exceptions\SolverException;
use App\Models\Draw;
use App\Models\Exclusion;
use App\Models\Participant;

it('throws an exception when there\'s no solution', function ($participants) {
    DrawFormHandler::withParticipants($participants)->save();
})->with('invalid participants list')->throws(SolverException::class);

it('does not record anything in case of error', function ($participants) {
    assertEquals(0, Draw::count());
    assertEquals(0, Participant::count());
    assertEquals(0, Exclusion::count());

    try { DrawFormHandler::withParticipants($participants)->save(); } catch (Exception $e) {}

    assertEquals(0, Draw::count());
    assertEquals(0, Participant::count());
    assertEquals(0, Exclusion::count());
})->with('invalid participants list');