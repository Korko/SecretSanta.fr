<?php

use App\Enums\DrawStatus;
use App\Models\Draw;
use App\Models\Participant;
use App\Notifications\DrawTitleChanged;

test('an organizer can confirm their email address', function () {
    Notification::fake();

    $draw = Draw::factory()
        ->createOne();

    expect($draw->organizer_email_verified_at)
        ->toBeNull();

    test()->get(URL::hashedSignedRoute('draw.confirmOrganizerEmail', ['draw' => $draw]))
        ->assertSuccessful();

    expect($draw->fresh()->organizer_email_verified_at)
        ->not()
        ->toBeNull();
});

test('if a participant organizer confirm their email address, it also confirm the according participant email', function () {
    Notification::fake();

    $draw = Draw::factory()
        ->withParticipantOrganizer()
        ->createOne();

    expect($draw->organizer->email_verified_at)
        ->toBeNull();

    test()->get(URL::hashedSignedRoute('draw.confirmOrganizerEmail', ['draw' => $draw]))
        ->assertSuccessful();

    expect($draw->organizer->fresh()->email_verified_at)
        ->not()
        ->toBeNull();
});

test('an organizer can change the title of the draw', function () {
    Notification::fake();

    $draw = Draw::factory()
        ->withParticipantOrganizer()
        ->withOrganizerEmailVerified()
        ->withVerifiedParticipants([
            fake()->name() => fake()->email(),
            fake()->name() => fake()->email(),
            fake()->name() => fake()->email(),
        ])
        ->createOne();

    $newValue = fake()->sentence();

    ajaxPost(URL::signedRoute('draw.changeTitle', ['draw' => $draw]), [
        'title' => $newValue
    ])
        ->assertSuccessful();

    expect($draw->fresh()->title)
        ->toBe($newValue);

    $draw->participantsNonOrganizer->each(function (Participant $participant) {
        Notification::assertSentTo($participant, DrawTitleChanged::class);
    });
});

test('an organizer can change their email any time before the pending draw is processed', function () {
    Notification::fake();

    $draw = Draw::factory()
        ->createOne();

    $newValue = fake()->email();

    ajaxPost(URL::signedRoute('draw.changeOrganizerEmail', ['draw' => $draw]), [
        'email' => $newValue
    ])
        ->assertSuccessful();

    expect($draw->fresh()->organizer_email)
        ->toBe($newValue);
});

test('if a participant organizer changes their email, it also change the according participant email', function () {
    Notification::fake();

    $draw = Draw::factory()
        ->withParticipantOrganizer()
        ->createOne();

    $newValue = fake()->email();

    ajaxPost(URL::signedRoute('draw.changeOrganizerEmail', ['draw' => $draw]), [
        'email' => $newValue
    ])
        ->assertSuccessful();

    expect($draw->organizer->fresh()->email)
        ->toBe($newValue);
});

test('if an organizer confirm their email address and then changes it again, it invalidates the new email address', function () {
    Notification::fake();

    $draw = Draw::factory()
        ->withOrganizerEmailVerified()
        ->createOne();

    ajaxPost(URL::signedRoute('draw.changeOrganizerEmail', ['draw' => $draw]), [
        'email' => fake()->email()
    ])
        ->assertSuccessful();

    expect($draw->fresh()->organizer_email_verified_at)
        ->toBeNull();
});

test('if a participant organizer confirm their email address and then changes it again, it also invalidates the according participant email', function () {
    Notification::fake();

    $draw = Draw::factory()
        ->withParticipantOrganizer()
        ->withOrganizerEmailVerified()
        ->createOne();

    $newValue = fake()->email();

    ajaxPost(URL::signedRoute('draw.changeOrganizerEmail', ['draw' => $draw]), [
        'email' => $newValue
    ])
        ->assertSuccessful();

    expect($draw->fresh()->organizer_email)
        ->toBe($newValue);
});

test('an organizer can change their name any time before the pending draw is processed', function () {
    Notification::fake();

    $draw = Draw::factory()
        ->createOne();

    $newValue = fake()->name();

    ajaxPost(URL::signedRoute('draw.changeOrganizerName', ['draw' => $draw]), [
        'name' => $newValue
    ])
        ->assertSuccessful();

    expect($draw->fresh()->organizer_name)
        ->toBe($newValue);
});

test('if a participant organizer changes their name, it also change the according participant name', function () {
    Notification::fake();

    $draw = Draw::factory()
        ->withParticipantOrganizer()
        ->createOne();

    $newValue = fake()->name();

    ajaxPost(URL::signedRoute('draw.changeOrganizerName', ['draw' => $draw]), [
        'name' => $newValue
    ])
        ->assertSuccessful();

    expect($draw->organizer->fresh()->name)
        ->toBe($newValue);
});

// TODO: Maybe a "change organizer" system?
test('an organizer cannot take the name of an already registered participant', function () {
    Notification::fake();

    $participantName = fake()->name();

    $draw = Draw::factory()
        ->withParticipants($participantName)
        ->createOne();

    ajaxPost(URL::signedRoute('draw.changeOrganizerName', ['draw' => $draw]), [
        'name' => $participantName
    ])
        ->assertUnprocessable()
        ->assertInvalid('name');

    expect($draw->organizer_name)
        ->toBe($draw->fresh()->organizer_name);
});

test('changing the organizer name doesn\'t invalidate their email', function () {
    Notification::fake();

    $draw = Draw::factory()
        ->withOrganizerEmailVerified()
        ->createOne();

    ajaxPost(URL::signedRoute('draw.changeOrganizerName', ['draw' => $draw]), [
        'name' => fake()->name()
    ])
        ->assertSuccessful();

    expect($draw->fresh()->organizer_email_verified_at)
        ->not()
        ->toBeNull();
});

test('an organizer can cancel a draw', function () {
    Notification::fake();

    $draw = Draw::factory()
        ->createOne();

    expect($draw->fresh()->status)
        ->toBe(DrawStatus::CREATED);

    ajaxPost(URL::signedRoute('draw.cancel', ['draw' => $draw]), [
        'name' => fake()->name()
    ])
        ->assertSuccessful();

    expect($draw->fresh()->status)
        ->toBe(DrawStatus::CANCELED);
});

test('an organizer can participate to a pending draw', function () {
    Notification::fake();

    $draw = Draw::factory()
        ->createOne();

    expect($draw->participantOrganizer)
        ->toBeFalse();

    ajaxPost(URL::signedRoute('draw.participate', ['draw' => $draw]))
        ->assertSuccessful();

    $draw = $draw->fresh();
    expect($draw->participantOrganizer)
        ->toBeTrue();
    expect($draw->organizer->name)
        ->toBe($draw->organizer_name);
    expect($draw->organizer->email)
        ->toBe($draw->organizer_email);
    expect($draw->organizer->email_verified_at)
        ->toBe($draw->organizer_email_verified_at);
});

test('a participant organizer can withdraw from participants', function () {
    Notification::fake();

    $draw = Draw::factory()
        ->withParticipantOrganizer()
        ->createOne();

    expect($draw->participantOrganizer)
        ->toBeTrue();

    ajaxPost(URL::signedRoute('draw.withdraw', ['draw' => $draw]))
        ->assertSuccessful();

    $draw = $draw->fresh();
    expect($draw->participantOrganizer)
        ->toBeFalse();
});

test('an organizer can prefill some participant names', function () {
    Notification::fake();

    $draw = Draw::factory()
        ->createOne();

    expect(Participant::class)->toHaveCount(0);

    ajaxPost(URL::signedRoute('draw.participant.add', ['draw' => $draw]), [
        'name' => fake()->name()
    ])
        ->assertSuccessful();

    expect(Participant::class)->toHaveCount(1);
});

test('when an organizer prefill some participant names, it updates the draw update date', function () {
    Notification::fake();

    $draw = Draw::factory()
        ->createOne();

    expect($draw->updated_at)
        ->toEqual($draw->created_at);

    $this->travel(1)->hour();

    ajaxPost(URL::signedRoute('draw.participant.add', ['draw' => $draw]), [
        'name' => fake()->name()
    ])
        ->assertSuccessful();

    expect($draw->fresh()->updated_at)
        ->not()
        ->toEqual($draw->created_at);
});

test('an organizer can remove some prefilled participant names', function () {
    Notification::fake();

    $draw = Draw::factory()
        ->withParticipants([
            fake()->name(),
            fake()->name(),
            fake()->name(),
            fake()->name(),
        ])
        ->createOne();

    $participant = $draw->participants->random();

    // Ask to remove one name
    ajaxDelete(URL::signedRoute('draw.participant.remove', ['draw' => $draw, 'participant' => $participant]))
        ->assertSuccessful();

    expect($participant->fresh())
        ->toBeNull();
});

test('when an organizer removes some participant names, it updates the draw update date', function () {
    Notification::fake();

    $draw = Draw::factory()
        ->withParticipants([
            fake()->name(),
        ])
        ->createOne();

    expect($draw->updated_at)
        ->toEqual($draw->created_at);

    $participant = $draw->participants->first();

    $this->travel(1)->hour();

    ajaxDelete(URL::signedRoute('draw.participant.remove', ['draw' => $draw, 'participant' => $participant]))
        ->assertSuccessful();

    expect($draw->fresh()->updated_at)
        ->not()
        ->toEqual($draw->created_at);
});
