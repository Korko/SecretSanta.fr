<?php

use App\Enums\DrawStatus;
use App\Models\Draw;
use App\Models\Participant;
use App\Notifications\DrawTitleChanged;

test('an organizer can confirm their email address', function () {
    Notification::fake();

    $draw = Draw::factory()
        ->createOne();

    expect($draw->organizer->email_verified_at)
        ->toBeNull();

    test()->get(URL::hashedSignedRoute('draw.participant.confirmEmail', ['draw' => $draw, 'participant' => $draw->organizer]))
        ->assertSuccessful();

    expect($draw->organizer->fresh()->email_verified_at)
        ->not()
        ->toBeNull();
});

test('an organizer can change the title of the draw', function () {
    Notification::fake();

    $draw = Draw::factory()
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

    $draw->santasNonOrganizer->each(function (Participant $participant) {
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

    expect($draw->organizer->fresh()->email_verified_at)
        ->toBeNull();
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

    expect($draw->organizer->name)
        ->toBe($draw->organizer->fresh()->name);
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

    expect($draw->organizer->fresh()->email_verified_at)
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
        ->withNonParticipantOrganizer()
        ->createOne();

    expect($draw->participant_organizer)
        ->toBeFalse();

    ajaxPost(URL::signedRoute('draw.participate', ['draw' => $draw]))
        ->assertSuccessful();

    $draw = $draw->fresh();
    expect($draw->participant_organizer)
        ->toBeTrue();
});

test('a participant organizer can withdraw from participants', function () {
    Notification::fake();

    $draw = Draw::factory()
        ->createOne();

    expect($draw->participant_organizer)
        ->toBeTrue();

    ajaxPost(URL::signedRoute('draw.withdraw', ['draw' => $draw]))
        ->assertSuccessful();

    $draw = $draw->fresh();
    expect($draw->participant_organizer)
        ->toBeFalse();
});

test('an organizer can prefill some participant names', function () {
    Notification::fake();

    $draw = Draw::factory()
        ->createOne();

    // Only 1 participant yet: the organizer
    expect(Participant::class)->toHaveCount(1);

    ajaxPost(URL::signedRoute('draw.participant.add', ['draw' => $draw]), [
        'name' => fake()->name()
    ])
        ->assertSuccessful();

    expect(Participant::class)->toHaveCount(2);
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

    $participant = $draw->santasNonOrganizer->random();

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
 
    expect(Participant::class)->toHaveCount(2);

    expect($draw->updated_at)
        ->toEqual($draw->created_at);

    $participant = $draw->santasNonOrganizer->first();

    $this->travel(1)->hour();

    ajaxDelete(URL::signedRoute('draw.participant.remove', ['draw' => $draw, 'participant' => $participant]))
        ->assertSuccessful();

    expect($draw->fresh()->updated_at)
        ->not()
        ->toEqual($draw->created_at);
});

test('an organizer can prefill some participant names and emails', function () {
    Notification::fake();

    $draw = Draw::factory()
        ->createOne();

    // Only 1 participant yet: the organizer
    expect(Participant::class)->toHaveCount(1);

    ajaxPost(URL::signedRoute('draw.participant.add', ['draw' => $draw]), [
        'name' => fake()->name(),
        'email' => fake()->email(),
    ])
        ->assertSuccessful();

    expect(Participant::class)->toHaveCount(2);
    expect($draw->santasNonOrganizer->first()->email)
        ->not()
        ->toBeNull();
});
