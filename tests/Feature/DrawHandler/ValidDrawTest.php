<?php

use App\Models\Draw;
use App\Models\Exclusion;
use App\Models\Participant;
use App\Notifications\TargetDrawn as TargetDrawnNotification;
use App\Services\DrawFormHandler;

it('records new entries in case of success', function ($participants, Draw $draw) {
    $exclusions = array_reduce($participants, function ($sum, $participant) { return $sum + count($participant['exclusions']); });

    assertModelExists($draw);
    assertModelCount(Participant::class, count($participants));
    assertModelCount(Exclusion::class, $exclusions);

    // Carreful, array is 0..n, Db is 1..n
    foreach($participants as $idx => $participant) {
        assertEquals($participant['name'], Participant::find($idx + 1)->name);
        assertEquals($participant['email'], Participant::find($idx + 1)->email);
    }
})->with('validated participants list');

it('saves the correct target', function ($participants, $targets) {
    $draw = createServiceDraw($participants);

    foreach($participants as $idx => $participant) {
        // idx (array offset, starting at 0) = id (database offset, starting at 1) - 1
        assertEquals($participants[$targets[$idx]]['name'], $draw->participants[$draw->participants[$idx]->target_id - 1]->name);
        assertEquals($participants[$targets[$idx]]['name'], $draw->participants[$idx]->target->name);

        assertEquals($participant['name'], $draw->participants[$draw->participants[$idx]->target_id - 1]->santa->name);
        assertEquals($participant['name'], $draw->participants[$idx]->target->santa->name);
    }
})->with('unique participants list');

it('send to each participant a link to write to their santa', function ($participants) {
    Notification::fake();

    $draw = createServiceDraw($participants);

    foreach($draw->participants as $participant) {
        Notification::assertSentTo($participant, function (TargetDrawnNotification $notification) use ($participant) {
            return $notification->toMail($participant)->assertSeeInHtml(
                URL::signedRoute('santa.view', ['participant' => $participant->hash]).'#'.base64_encode(DrawCrypt::getIV())
            );
        });
    }
})->with('participants list');