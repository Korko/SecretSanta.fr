<?php

use App\Notifications\ConfirmWithdrawal;
use App\Notifications\DearSanta;
use App\Notifications\TargetDrawn;
use App\Notifications\TargetNameChanged;
use App\Notifications\TargetWithdrawn;
use App\Models\Draw;

test('the organizer can send again the target drawn email', function (Draw $draw) {
    Notification::fake();

    $participant = $draw->participants->random();

    $path = URL::signedRoute('organizerPanel.changeEmail', [
        'draw' => $draw,
        'participant' => $participant,
    ]);

    ajaxPost($path, ['email' => $participant->email])
        ->assertSuccessful()
        ->assertJsonStructure(['message']);

    Notification::assertSentTo($participant, TargetDrawn::class);
})->with('basic draw');

test('the organizer can change a participant\'s email', function (Draw $draw) {
    Notification::fake();

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
})->with('basic draw');

test('the organizer can change a participant\'s name', function (Draw $draw) {
    Notification::fake();

    $participant = $draw->participants->random();

    $path = URL::signedRoute('organizerPanel.changeName', [
        'draw' => $draw,
        'participant' => $participant,
    ]);

    $before = $participant->name;

    ajaxPost($path, ['name' => 'foobar'])
        ->assertSuccessful()
        ->assertJsonStructure(['message']);

    $participant = $participant->fresh();
    $after = $participant->name;

    assertNotEquals($before, $after);
    assertEquals('foobar', $after);

    Notification::assertSentTo($participant->santa, TargetNameChanged::class);
})->with('basic draw');

test('the organizer cannot give twice the same name', function (Draw $draw) {
    Notification::fake();

    [$participant, $other] = $draw->participants->random(2);

    $path = URL::signedRoute('organizerPanel.changeName', [
        'draw' => $draw,
        'participant' => $participant,
    ]);

    $before = $participant->name;

    ajaxPost($path, ['name' => $other->name])
        ->assertUnprocessable()
        ->assertJsonStructure(['message']);

    $participant = $participant->fresh();
    $after = $participant->name;

    assertEquals($before, $after);

    Notification::assertNothingSent();
})->with('basic draw');

test('the organizer can withdraw a participant', function (Draw $draw) {
    Notification::fake();

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
})->with('large draw');

test('the organizer can download initial data', function (Draw $draw) {
    $path = URL::signedRoute('organizerPanel.csvInit', [
        'draw' => $draw,
    ]);

    ajaxGet($path)
        ->assertHeader('Content-Type', 'text/csv; charset=UTF-8')
        ->assertSuccessful();
})->with('basic draw');

test('the organizer cannot download total data if the draw is not finished', function (Draw $draw) {
    $path = URL::signedRoute('organizerPanel.csvFinal', [
        'draw' => $draw,
    ]);

    ajaxGet($path)->assertStatus(403);
})->with('basic draw');

test('the organizer can download total data if the draw is finished', function (Draw $draw) {
    $path = URL::signedRoute('organizerPanel.csvFinal', [
        'draw' => $draw,
    ]);

    ajaxGet($path)
        ->assertHeader('Content-Type', 'text/csv; charset=UTF-8')
        ->assertSuccessful();
})->with('finished draw');

test('the organizer can delete all data before the event', function (Draw $draw) {
    $path = URL::signedRoute('organizerPanel.delete', [
        'draw' => $draw,
    ]);

    ajaxDelete($path)
        ->assertSuccessful()
        ->assertJsonStructure(['message']);

    assertNull($draw->fresh());
})->with('basic draw');

test('the organizer can delete all data after the event', function (Draw $draw) {
    $path = URL::signedRoute('organizerPanel.delete', [
        'draw' => $draw,
    ]);

    ajaxDelete($path)
        ->assertSuccessful()
        ->assertJsonStructure(['message']);

    assertNull($draw->fresh());
})->with('finished draw');

it('updates the draw update date when sending an email', function (Draw $draw) {
    Notification::fake();

    $updated_at = $draw->updated_at;
    $participant = $draw->participants->first();

    sleep(2);

    ajaxPost(URL::signedRoute('organizerPanel.changeEmail', [
            'draw' => $draw,
            'participant' => $participant,
        ]), ['email' => 'test@test2.com'])
        ->assertSuccessful()
        ->assertJsonStructure(['message']);

    Notification::assertSentToTimes($participant, TargetDrawn::class, 1);
    test()->assertNotEquals($updated_at->timestamp, $draw->fresh()->updated_at->timestamp);
})->with('basic draw');