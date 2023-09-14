<?php

use App\Models\Draw;
use App\Models\Participant;
use App\Notifications\PendingDrawConfirm;

test('a visitor can create a new pending draw', function () {
    Notification::fake();

    // Ensure we have no draws in database yet
    expect(Draw::class)->toHaveCount(0);

    // Try to create a new draw
    ajaxPost(URL::route('form.process'), [
        'title' => fake()->sentence(),
        'description' => fake()->sentence(),
        'organizer-name' => fake()->name(),
        'organizer-email' => fake()->email(),
    ])->assertJsonStructure(['message', 'link', 'qrcode']);

    // Ensure we have a new draw in database
    expect(Draw::class)->toHaveCount(1);
});

test('an organizer can specify a list of participant names', function () {
    Notification::fake();

    // Ensure we have no draws nor participants in database yet
    expect(Draw::class)->toHaveCount(0);
    expect(Participant::class)->toHaveCount(0);

    // Try to create a new draw
    ajaxPost(URL::route('form.process'), [
        'title' => fake()->sentence(),
        'description' => fake()->sentence(),
        'organizer-name' => fake()->name(),
        'organizer-email' => fake()->email(),
        'participants' => [
            fake()->name(),
            fake()->name(),
            fake()->name(),
        ],
    ])->assertSuccessful();

    // Ensure we have a new draw in database with corresponding participants
    expect(Draw::class)->toHaveCount(1);
    expect(Participant::class)->toHaveCount(3);
});

test('an organizer cannot specify a list of participant names containing their own name', function () {
    Notification::fake();

    // Ensure we have no draws nor participants in database yet
    expect(Draw::class)->toHaveCount(0);
    expect(Participant::class)->toHaveCount(0);

    $organizer = fake()->name();

    // Try to create a new draw, ensure it fails
    ajaxPost(URL::route('form.process'), [
        'title' => fake()->sentence(),
        'description' => fake()->sentence(),
        'organizer-name' => $organizer,
        'organizer-email' => fake()->email(),
        'participants' => [
            fake()->name(),
            $organizer,
            fake()->name(),
        ],
    ])
        ->assertUnprocessable()
        ->assertInvalid('participants.1');

    // Ensure we still have no draws nor participants in database
    expect(Draw::class)->toHaveCount(0);
    expect(Participant::class)->toHaveCount(0);
});

test('an organizer cannot specify a list of participant names with duplicates', function () {
    Notification::fake();

    // Ensure we have no draws nor participants in database yet
    expect(Draw::class)->toHaveCount(0);
    expect(Participant::class)->toHaveCount(0);

    $participantName = fake()->name();

    // Try to create a new draw, ensure it fails
    ajaxPost(URL::route('form.process'), [
        'title' => fake()->sentence(),
        'description' => fake()->sentence(),
        'organizer-name' => fake()->name(),
        'organizer-email' => fake()->email(),
        'participants' => [
            $participantName,
            $participantName,
            fake()->name(),
        ],
    ])
        ->assertUnprocessable()
        ->assertInvalid('participants.0')
        ->assertInvalid('participants.1');

    // Ensure we still have no draws nor participants in database
    expect(Draw::class)->toHaveCount(0);
    expect(Participant::class)->toHaveCount(0);
});

test('an organizer can also be a participant', function () {
    Notification::fake();

    // Try to create a new draw, ensure it fails
    expect(Draw::class)->toHaveCount(0);
    expect(Participant::class)->toHaveCount(0);

    ajaxPost(URL::route('form.process'), [
        'participant-organizer' => true,
        'title' => fake()->sentence(),
        'description' => fake()->sentence(),
        'organizer-name' => fake()->name(),
        'organizer-email' => fake()->email(),
        'participants' => [
            fake()->name(),
            fake()->name(),
            fake()->name(),
        ],
    ])->assertSuccessful();

    expect(Draw::class)->toHaveCount(1);
    expect(Participant::class)->toHaveCount(4);
});

test('an organizer is informed they must confirm their email address to process a pending draw', function () {
    Notification::fake();

    // Try to create a new draw
    ajaxPost(URL::route('form.process'), [
        'title' => fake()->sentence(),
        'description' => fake()->sentence(),
        'organizer-name' => fake()->name(),
        'organizer-email' => fake()->email(),
    ])->assertSuccessful();

    Notification::assertSentTo(Draw::first()->organizer, PendingDrawConfirm::class);
});

test('an organizer receives in the original notification the link to confirm their email address', function () {
    Notification::fake();

    // Try to create a new draw
    ajaxPost(URL::route('form.process'), [
        'title' => fake()->sentence(),
        'description' => fake()->sentence(),
        'organizer-name' => fake()->name(),
        'organizer-email' => fake()->email(),
    ])->assertSuccessful();

    Notification::assertSentTo(Draw::first()->organizer, PendingDrawConfirm::class, function ($notification, $channels, $notifiable) {
        return
            $notification->toMail($notifiable)->assertSeeInHtml(
                URL::hashedSignedRoute('draw.confirmOrganizerEmail', ['draw' => Draw::first()])
            );
    });
});

test('a draw expires after a certain amount of time', function () {
    Notification::fake();

    $draw = Draw::factory()
        ->createOne();

    $this->artisan('model:prune', ['--model' => [Draw::class]])->assertSuccessful();

    expect($draw->fresh())
        ->not()
        ->toBeNull();

    $this->travel(2)->months();

    $this->artisan('model:prune', ['--model' => [Draw::class]])->assertSuccessful();

    expect($draw->fresh())
        ->toBeNull();
});