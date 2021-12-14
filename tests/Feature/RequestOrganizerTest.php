<?php

use App\Notifications\ConfirmWithdrawal;
use App\Notifications\DearSanta;
use App\Notifications\TargetDrawn;
use App\Notifications\TargetWithdrawn;
use App\Models\Draw;
use App\Models\Participant;

test('the organizer can send again the target drawn email', function () {
    Notification::fake();

    $draw = Draw::factory()
        ->hasParticipants(3)
        ->create();
    $participant = $draw->participants->random();

    $path = URL::signedRoute('organizerPanel.changeEmail', [
        'draw' => $draw,
        'participant' => $participant,
    ]);

    ajaxPost($path, ['email' => $participant->email])
        ->assertSuccessful()
        ->assertJsonStructure(['message']);

    Notification::assertSentTo($participant, TargetDrawn::class);
});

test('the organizer can change a participant\'s email', function () {
    Notification::fake();

    $draw = Draw::factory()
        ->hasParticipants(3)
        ->create();
    $participant = $draw->participants->random();

    $path = URL::signedRoute('organizerPanel.changeEmail', [
        'draw' => $draw,
        'participant' => $participant,
    ]);

    $before = $participant->email;

    ajaxPost($path, ['email' => 'test@test2.com'])
        ->assertSuccessful()
        ->assertJsonStructure(['message']);

    $participant = $participant->fresh();
    $after = $participant->email;

    assertNotEquals($before, $after);
    assertEquals('test@test2.com', $after);

    Notification::assertSentTo($participant, TargetDrawn::class);
});

test('the organizer can withdraw a participant', function () {
    Notification::fake();

    $draw = Draw::factory()
        ->hasParticipants(4)
        ->create();
    $participant = $draw->participants->random();

    ajaxPost(URL::signedRoute('dearSanta.contact', ['participant' => $participant]), [
            'content' => 'test dearSanta mail content',
        ])
        ->assertSuccessful()
        ->assertJsonStructure(['message']);

    Notification::assertSentTo($participant->santa, DearSanta::class);

    $santa = $participant->santa;
    $target = $participant->target;

    ajaxGet(URL::signedRoute('organizerPanel.withdraw', [
            'draw' => $draw,
            'participant' => $participant,
        ]))
        ->assertSuccessful()
        ->assertJsonStructure(['message']);

    Notification::assertSentTo($santa, TargetWithdrawn::class);
    Notification::assertSentTo($santa, DearSanta::class);
    Notification::assertSentTo($participant, ConfirmWithdrawal::class);

    assertEquals($santa->fresh()->target->id, $target->id);
    assertEquals($target->fresh()->santa->id, $santa->id);
    assertEquals($participant->fresh(), null);
});

test('the organizer can download initial data', function () {
    $draw = Draw::factory()
        ->hasParticipants(3)
        ->create();

    $path = URL::signedRoute('organizerPanel.csvInit', [
        'draw' => $draw,
    ]);

    ajaxGet($path)
        ->assertHeader('Content-Type', 'text/csv; charset=UTF-8')
        ->assertSuccessful();
});

test('the organizer cannot download total data if the draw is not expired', function () {
    $draw = Draw::factory()
        ->hasParticipants(3)
        ->create();

    $path = URL::signedRoute('organizerPanel.csvFinal', [
        'draw' => $draw,
    ]);

    ajaxGet($path)->assertStatus(403);
});

test('the organizer can download total data if the draw is expired', function () {
    $draw = Draw::factory()
        ->hasParticipants(3)
        ->expired()
        ->create();

    $path = URL::signedRoute('organizerPanel.csvFinal', [
        'draw' => $draw,
    ]);

    ajaxGet($path)
        ->assertHeader('Content-Type', 'text/csv; charset=UTF-8')
        ->assertSuccessful();
});

test('the organizer can delete all data before expiration', function () {
    $draw = Draw::factory()
        ->hasParticipants(3)
        ->create();

    $path = URL::signedRoute('organizerPanel.delete', [
        'draw' => $draw,
    ]);

    ajaxDelete($path)
        ->assertSuccessful()
        ->assertJsonStructure(['message']);

    assertNull($draw->fresh());
});

test('the organizer can delete all data after expiration', function () {
    $draw = Draw::factory()
        ->hasParticipants(3)
        ->expired()
        ->create();

    $path = URL::signedRoute('organizerPanel.delete', [
        'draw' => $draw,
    ]);

    ajaxDelete($path)
        ->assertSuccessful()
        ->assertJsonStructure(['message']);

    assertNull($draw->fresh());
});