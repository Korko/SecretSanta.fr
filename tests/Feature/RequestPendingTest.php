<?php

use App\Enums\DrawStatus;
use App\Models\Draw;
use App\Models\Participant;
use App\Notifications\PendingDrawConfirm;

test('a visitor can create a new pending draw', function () {
    Notification::fake();

    expect(Draw::class)->toHaveCount(0);

    ajaxPost(URL::route('form.process'), [
        'title' => fake()->sentence(),
        'organizer-name' => fake()->name(),
        'organizer-email' => fake()->email(),
    ])->assertJsonStructure(['message', 'link', 'qrcode']);

    expect(Draw::class)->toHaveCount(1);
});

test('an organizer can specify a list of participant names', function () {
    Notification::fake();

    expect(Draw::class)->toHaveCount(0);
    expect(Participant::class)->toHaveCount(0);

    ajaxPost(URL::route('form.process'), [
        'title' => fake()->sentence(),
        'organizer-name' => fake()->name(),
        'organizer-email' => fake()->email(),
        'participants' => [
            fake()->name(),
            fake()->name(),
            fake()->name(),
        ],
    ])->assertSuccessful();

    expect(Draw::class)->toHaveCount(1);
    expect(Participant::class)->toHaveCount(3);
});

test('an organizer cannot specify a list of participant names containing their own name', function () {
    Notification::fake();

    expect(Draw::class)->toHaveCount(0);
    expect(Participant::class)->toHaveCount(0);

    $organizer = fake()->name();

    ajaxPost(URL::route('form.process'), [
        'title' => fake()->sentence(),
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

    expect(Draw::class)->toHaveCount(0);
    expect(Participant::class)->toHaveCount(0);
});

test('an organizer cannot specify a list of participant names with duplicates', function () {
    Notification::fake();

    expect(Draw::class)->toHaveCount(0);
    expect(Participant::class)->toHaveCount(0);

    $participantName = fake()->name();

    ajaxPost(URL::route('form.process'), [
        'title' => fake()->sentence(),
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

    expect(Draw::class)->toHaveCount(0);
    expect(Participant::class)->toHaveCount(0);
});

test('an organizer can also be a participant', function () {
    Notification::fake();

    expect(Draw::class)->toHaveCount(0);
    expect(Participant::class)->toHaveCount(0);

    ajaxPost(URL::route('form.process'), [
        'participant-organizer' => true,
        'title' => fake()->sentence(),
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

    ajaxPost(URL::route('form.process'), [
        'title' => fake()->sentence(),
        'organizer-name' => fake()->name(),
        'organizer-email' => fake()->email(),
    ])->assertSuccessful();

    Notification::assertSentTo(Draw::first()->organizer, PendingDrawConfirm::class);
});

test('an organizer receives in the original notification the link to confirm their email address', function () {
    Notification::fake();

    ajaxPost(URL::route('form.process'), [
        'title' => fake()->sentence(),
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

// TODO: Add tests about changing title

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
    // Organizer is not participant
    // TODO

    // Asks to be participant
    // TODO

    // Check organizer_id filled, name and email same as organizer in pending_draw
    // TODO
})->todo();

test('a participant organizer can withdraw from participants', function () {
    // Organizer is participant
    // TODO

    // Asks to withdraw
    // TODO

    // Check organizer_id empty and participant removed
    // TODO

    // Ensure organizer name and email still filled in pending_draw
    // TODO
})->todo();

test('an organizer can prefill some participant names', function () {
    Notification::fake();

    $draw = Draw::factory()
        ->createOne();

    ajaxPost(URL::signedRoute('draw.addParticipantName', ['draw' => $draw]), [
        'name' => fake()->name()
    ])
        ->assertSuccessful();
});

test('an organizer can remove some prefilled participant names', function () {
    Notification::fake();

    $participantNames = [
        fake()->name(),
        fake()->name(),
        fake()->name(),
        fake()->name(),
    ];

    $draw = Draw::factory()
        ->withParticipants($participantNames)
        ->createOne();

    // Pick a random name from the list
    $name = array_rand($participantNames);

    expect($draw->participants()->pluck('name'))
        ->toContain($name);

    // Ask to remove one name
    // TODO

    expect($draw->participants()->pluck('name'))
        ->not()
        ->toContain($name);
})->todo();

test('a visitor can join a pending draw', function () {
    // Check the url from the organizer confirmation is valid
    // TODO

    // Check a visitor can register with a new name
    // TODO

    // Check that participant is now registered and status changed
    // TODO

    // Check that participant received a mail after choosing their name
    // TODO
})->todo();

test('a visitor can join a pending draw by selecting an already filled name', function () {
    // Create a draw with names defined
    // TODO

    // Check a visitor can pick a name defined by the organizer
    // TODO

    // Check that participant is now registered and status changed
    // TODO

    // Check that participant received a mail after choosing their name
    // TODO
})->todo();

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

///// Organizer actions

test('an organizer can request a link to reset a participant email', function () {

})->todo();

test('a participant can reset their email with the link given by the organizer', function () {

})->todo();

test('a participant have to confirm their email to validate the change', function () {

})->todo();

test('an organizer can send a direct message to a participant', function () {

})->todo();

test('an organizer can duplicate a draw and its participants', function () {

})->todo();

test('a duplicated draw participants must confirm their participation', function () {

})->todo();

///// Participant actions

test('a participant can change their name and the organizer is notified', function () {

})->todo();

test('a participant can send a message to their santa', function () {

})->todo();

test('a participant can request the removal of their private data without leaving a draw and the organizer is informed', function () {

})->todo();


test('if a participant asked for their private data removal, any participant from the same draw will be notified if they try to contact their santa', function () {

})->todo();

test('if a participant asked for their private data removal, amongst all the participants from the same draw, only their santa will be notified if they try to contact their target', function () {

})->todo();

test('a participant can answer to a dearTarget message directly from their email', function () {

})->todo();

// TODO: No idea how to do it, yet
test('a santa should be able to answer a dearSanta message directly from their email', function () {

})->todo();

///// Paid system

test('a free pending draw cannot accept more than the free limit of participants', function () {

})->todo();

test('a paid pending draw can accept more than the free limit of participants', function () {

})->todo();
