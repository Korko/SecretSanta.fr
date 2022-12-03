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

it('can handle several exclusions', function () {
    $participants = [
        ['name' => uniqid(), 'email' => 'test@test.com', 'exclusions' => [11,22,16,18,8,9,5,4,2,1,3,7,12,23,21,20,19,14]],
        ['name' => uniqid(), 'email' => 'test@test.com', 'exclusions' => []],
        ['name' => uniqid(), 'email' => 'test@test.com', 'exclusions' => []],
        ['name' => uniqid(), 'email' => 'test@test.com', 'exclusions' => []],
        ['name' => uniqid(), 'email' => 'test@test.com', 'exclusions' => []],
        ['name' => uniqid(), 'email' => 'test@test.com', 'exclusions' => []],
        ['name' => uniqid(), 'email' => 'test@test.com', 'exclusions' => []],
        ['name' => uniqid(), 'email' => 'test@test.com', 'exclusions' => [11]],
        ['name' => uniqid(), 'email' => 'test@test.com', 'exclusions' => []],
        ['name' => uniqid(), 'email' => 'test@test.com', 'exclusions' => []],
        ['name' => uniqid(), 'email' => 'test@test.com', 'exclusions' => []],
        ['name' => uniqid(), 'email' => 'test@test.com', 'exclusions' => []],
        ['name' => uniqid(), 'email' => 'test@test.com', 'exclusions' => []],
        ['name' => uniqid(), 'email' => 'test@test.com', 'exclusions' => []],
        ['name' => uniqid(), 'email' => 'test@test.com', 'exclusions' => [11]],
        ['name' => uniqid(), 'email' => 'test@test.com', 'exclusions' => []],
        ['name' => uniqid(), 'email' => 'test@test.com', 'exclusions' => []],
        ['name' => uniqid(), 'email' => 'test@test.com', 'exclusions' => []],
        ['name' => uniqid(), 'email' => 'test@test.com', 'exclusions' => []],
        ['name' => uniqid(), 'email' => 'test@test.com', 'exclusions' => []],
        ['name' => uniqid(), 'email' => 'test@test.com', 'exclusions' => []],
        ['name' => uniqid(), 'email' => 'test@test.com', 'exclusions' => [1,2,3,4,5,6,7,8,23,20,19,18,16,15,14,13,12,11,10,17,9,22]],
        ['name' => uniqid(), 'email' => 'test@test.com', 'exclusions' => []],
        ['name' => uniqid(), 'email' => 'test@test.com', 'exclusions' => []],
    ];

    $this->assertNotEquals(null, createServiceDraw($participants));
});
