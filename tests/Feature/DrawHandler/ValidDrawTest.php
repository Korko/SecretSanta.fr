<?php

use App\Models\Draw;
use App\Models\Participant;
use App\Notifications\TargetDrawn as TargetDrawnNotification;

it('records new entries in case of success', function ($participants, Draw $draw) {
    $exclusions = array_reduce($participants, function ($sum, $participant) {
        return $sum + count($participant['exclusions']);
    });

    assertModelExists($draw);
    assertModelCount(Participant::class, count($participants));

    $indb = Participant::all();

    foreach ($participants as $idx => $participant) {
        assertEquals($participant['name'], $indb[$idx]->name);
        assertEquals($participant['email'], $indb[$idx]->email);
    }
})->with('validated participants list');

it('saves the correct target', function ($participants, $targets) {
    $draw = createServiceDraw($participants);

    foreach ($participants as $idx => $participant) {
        assertEquals($participants[$targets[$idx]]['name'], $draw->participants[$idx]->target->name);
        assertEquals($participant['name'], $draw->participants[$idx]->target->santa->name);
    }
})->with('unique participants list');

it('send to each participant a link to write to their santa', function ($participants) {
    Notification::fake();

    $draw = createServiceDraw($participants);

    foreach ($draw->participants as $participant) {
        Notification::assertSentTo($participant, function (TargetDrawnNotification $notification) use ($participant) {
            return $notification->toMail($participant)->assertSeeInHtml(
                URL::signedRoute('santa.index', ['participant' => $participant->hash]).'#'.base64_encode(DrawCrypt::getIV())
            );
        });
    }
})->with('participants list');
