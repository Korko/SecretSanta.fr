<?php

use App\Exceptions\SolverException;
use App\Models\Draw;
use App\Models\Exclusion;
use App\Models\Participant;
use App\Services\DrawFormHandler;

it('throws an exception when there\'s no solution', function ($participants) {
    (new DrawFormHandler())->withParticipants($participants)->save();
})->with('invalid participants list')->throws(SolverException::class);

it('does not record anything in case of error', function ($participants) {
    assertModelCount(Draw::class, 0);
    assertModelCount(Participant::class, 0);
    assertModelCount(Exclusion::class, 0);

    try { (new DrawFormHandler())->withParticipants($participants)->save(); } catch (Exception $e) {}

    assertModelCount(Draw::class, 0);
    assertModelCount(Participant::class, 0);
    assertModelCount(Exclusion::class, 0);
})->with('invalid participants list');