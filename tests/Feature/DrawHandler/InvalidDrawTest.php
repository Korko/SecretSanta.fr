<?php

use App\Exceptions\SolverException;
use App\Models\Draw;
use App\Models\Exclusion;
use App\Models\Participant;
use App\Services\DrawFormHandler;

use function PHPUnit\Framework\assertEquals;

it("throws an exception when there's no solution", function ($participants): void {
    (new DrawFormHandler)->withParticipants($participants)->save();
})->with('invalid participants list')->throws(SolverException::class);

it('does not record anything in case of error', function ($participants): void {
    assertEquals(0, Draw::query()->count());
    assertEquals(0, Participant::query()->count());
    assertEquals(0, Exclusion::query()->count());

    try {
        (new DrawFormHandler)->withParticipants($participants)->save();
    } catch (Exception) {
    }

    assertEquals(0, Draw::query()->count());
    assertEquals(0, Participant::query()->count());
    assertEquals(0, Exclusion::query()->count());
})->with('invalid participants list');
