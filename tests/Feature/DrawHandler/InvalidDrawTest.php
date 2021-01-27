<?php

use App\Models\Draw;
use App\Models\Exclusion;
use App\Models\Participant;

it('throws and exception when there\'s no solution', function ($participants) {
    DrawHandler::toParticipants($participants);
})->with('invalid participants list')->throws(App\Exceptions\SolverException::class);

it('does not record anything in case of error', function ($participants) {
    assertEquals(0, Draw::count());
    assertEquals(0, Participant::count());
    assertEquals(0, Exclusion::count());

    try { DrawHandler::toParticipants($participants); } catch (Exception $e) {}

    assertEquals(0, Draw::count());
    assertEquals(0, Participant::count());
    assertEquals(0, Exclusion::count());
})->with('invalid participants list');

it('does not send emails in case of error', function ($participants) {
    Notification::fake();
    Notification::assertNothingSent();

    try { DrawHandler::toParticipants($participants); } catch (Exception $e) {}
})->with('invalid participants list');
