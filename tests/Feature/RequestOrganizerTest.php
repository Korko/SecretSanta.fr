<?php

use App\Mail\TargetDrawn;
use App\Models\Participant;

test('the organizer can send again the target drawn email', function () {
    Mail::fake();

    $draw = createDatabaseDraw(3);
    $participant = $draw->participants->random();

    // Check data can be changed
    $path = URL::signedRoute('organizerPanel.changeEmail', [
        'draw' => $draw->hash,
        'participant' => $participant->id,
    ]);

    ajaxPost($path, ['email' => $participant->email])
        ->assertJsonStructure(['message'])
        ->assertStatus(200);

    assertHasMailPushed(TargetDrawn::class, $participant->email);
});

test('the organizer can change a participant\'s email', function () {
    Mail::fake();

    $draw = createDatabaseDraw(3);
    $participant = $draw->participants->random();

    // Check data can be changed
    $path = URL::signedRoute('organizerPanel.changeEmail', [
        'draw' => $draw->hash,
        'participant' => $participant->id,
    ]);

    ajaxPost($path, ['email' => 'test@test2.com'])
        ->assertJsonStructure(['message'])
        ->assertStatus(200);

    $before = $participant->email;
    $participant = Participant::find($participant->id);
    $after = $participant->email;

    assertNotEquals($before, $after);
    assertEquals('test@test2.com', $after);

    assertHasMailPushed(TargetDrawn::class, $participant->email);
});

test('the organizer can download initial data');
test('the organizer cannot download total data if the draw is not expired');
test('the organizer can download total data if the draw is expired');

test('the organizer can delete all data');
