<?php

use App\Models\Draw;
use App\Models\Participant;
use App\Notifications\TargetDrawn as TargetDrawnNotification;

it('records new entries in case of success', function ($participants, Draw $draw) {
    Notification::fake();

    expect($draw)->toExists();
    expect(Participant::class)->toHaveCount(count($participants) + 1);

    $indb = Participant::all();

    foreach ($participants as $idx => $participant) {
        expect($participant['name'])->toBe($indb[$idx]->name);
        expect($participant['email'])->toBe($indb[$idx]->email);
    }
})->with('validated participants list');

it('saves the correct target', function ($participants, $targets) {
    Notification::fake();

    $draw = createServiceDraw($participants);

    foreach ($participants as $idx => $participant) {
        expect($draw->santas[$idx]->target->name)->toBe($participants[$targets[$idx]]['name']);
        expect($draw->santas[$idx]->target->santa->name)->toBe($participant['name']);
    }
})->with('unique participants list');

it('send to each participant a link to write to their santa', function ($participants) {
    Notification::fake();

    $draw = createServiceDraw($participants);

    foreach ($draw->santas as $participant) {
        Notification::assertSentTo($participant, function (TargetDrawnNotification $notification) use ($participant) {
            $notification->toMail($participant)->assertSeeInHtml(
                URL::hashedRoute('participant.index', ['participant' => $participant])
            );
            return true;
        });
    }
})->with('participants list');

it('can handle several exclusions', function () {
    Notification::fake();

    $participants = [
        ['name' => uniqid(), 'email' => 'test@test.com', 'exclusions' => [11, 22, 16, 18, 8, 9, 5, 4, 2, 1, 0, 3, 7, 12, 23, 21, 20, 19, 14]],
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
        ['name' => uniqid(), 'email' => 'test@test.com', 'exclusions' => [1, 2, 3, 4, 5, 6, 7, 8, 23, 20, 19, 18, 16, 15, 14, 13, 12, 11, 10, 17, 9, 22]], // Left 0
        ['name' => uniqid(), 'email' => 'test@test.com', 'exclusions' => []],
        ['name' => uniqid(), 'email' => 'test@test.com', 'exclusions' => []],
    ];

    $this->assertNotEquals(null, createServiceDraw($participants));
});

