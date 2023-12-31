<?php

use App\Models\Draw;

test('a visitor can join a pending draw', function () {
    Notification::fake();

    $draw = Draw::factory()
        ->createOne();

    // Check the url from the organizer confirmation is valid
    // TODO

    $name = fake()->name();
    $email = fake()->email();

    // Check a visitor can register with a new name
    ajaxPost(URL::signedRoute('pending.join.handle', ['draw' => $draw]), [
        'name' => $name,
        'email' => $email
    ])
        ->assertSuccessful();

    // Check that participant is now registered and status changed
    $participant = $draw
        ->santas
        ->first();

    // Cannot make a where condition on name or email as they are crypted on database
    // So get the first participant and check them once decrypted.
    expect($participant)
        ->not()
        ->toBeNull();
    expect($participant->name)
        ->toBe($name);
    expect($participant->email)
        ->toBe($email);
    expect($participant->email_verified_at)
        ->toBeNull();

    // Check that participant received a mail after choosing their name
    // TODO Notification::assertSentTo($participant, )
});

test('a visitor can join a pending draw by selecting an already filled name', function () {
    // Create a draw with names defined
    Notification::fake();

    $names = [
        fake()->name(),
        fake()->name(),
        fake()->name()
    ];

    $draw = Draw::factory()
        ->withParticipants($names)
        ->createOne();

    // Check a visitor can pick a name defined by the organizer
    $name = $names[array_rand($names, 1)];
    $email = fake()->email();

    $participant = $draw
        ->participants
        ->where(fn($participant) => $participant->name === $name)
        ->first();

    expect($participant)
        ->not()
        ->toBeNull();

    expect($participant->email)
        ->toBeNull();

    // Check a visitor can register with a new name
    ajaxPost(URL::signedRoute('pending.join.handle', ['draw' => $draw]), [
        'name' => $name,
        'email' => $email
    ])
        ->assertSuccessful();

    // Check that participant is now registered and status changed
    $participant = $participant->fresh();
    expect($participant->email)
        ->toBe($email);
    expect($participant->email_verified_at)
        ->toBeNull();

    // Check that participant received a mail after choosing their name
    // TODO
});

test('once a visitor confirm their participation, the organizer is notified', function () {
    // Create a draw
    // TODO

    // A visitor can pick a name defined by the organizer
    // TODO

    // Check organizer notification
    // TODO

    // Same as before but with participant organizer
    // TODO
})->todo();

test('a visitor cannot join an already started draw', function () {

})->todo();

test('a visitor cannot join a finished draw', function () {

})->todo();

test('a visitor cannot join a cancelled draw', function () {

})->todo();

test('a visitor have to confirm their email to validate their participation to the draw', function () {
    // Check the visitor can confirm their email
    // TODO

    // Check the participant status has changed and is now locked
    // TODO

    // Check a visitor received a link to their panel once email was validated
    // TODO

    // Check a new User is created with a new UserEmail after validation
    // TODO
})->todo();

test('a participant name is freed after some time if the visitor didn\'t confirm their email', function () {
    // Create participant with email not validated
    // TODO

    // Time advance
    // TODO

    // Cron job
    // TODO

    // Check participant email is removed, status reset
    // TODO
})->todo();

test('a participant can change their name any time before the pending draw is processed', function () {
    // Create participant with email validated
    // TODO

    // Check participant can update their name
    // TODO

    // Check name was changed
    // TODO
})->todo();

test('the organizer is notified when a visitor changes their name before the pending draw is processed', function () {
    // Create participant with email validated
    // TODO

    // Check participant can update their name
    // TODO

    // Check the organizer received a notification
    // TODO
})->todo();

test('a participant can change their email any time before the pending draw is processed', function () {
    // Create participant with email validated
    // TODO

    // Check participant can update their email
    // TODO

    // Check the email is changed
    // TODO
})->todo();

test('if a participant confirm their email address and then changes it again, it invalidates the new email address', function () {
    // Create participant with email validated
    // TODO

    // Check participant can update their email
    // TODO

    // Check the email status is invalidated
    // TODO

    // Validate the new email
    // TODO

    // Check the email is validated
    // TODO
})->todo();
