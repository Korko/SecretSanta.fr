<?php

use App\Exceptions\SolverException;
use App\Models\Draw;
use App\Models\Participant;

it('throws an exception when there\'s no solution', function ($participants) {
    createServiceDraw($participants);
})->with('invalid participants list')->throws(SolverException::class)->todo();

it('does not record anything in case of error', function ($participants) {
    expect(Draw::class)->toHaveCount(0);
    expect(Participant::class)->toHaveCount(0);

    try {
        createServiceDraw($participants);
    } catch (Exception $e) {
    }

    expect(Draw::class)->toHaveCount(0);
    expect(Participant::class)->toHaveCount(0);
})->with('invalid participants list')->todo();
