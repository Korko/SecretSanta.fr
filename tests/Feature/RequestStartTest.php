<?php

use App\Notifications\OrganizerRecap;
use App\Notifications\TargetDrawn;
use Illuminate\Notifications\AnonymousNotifiable;

it('sends no notifications in case of error', function ($participants) {
    Notification::fake();

    $pendingDraw = createPendingDraw($participants);

    ajaxGet(URL::signedRoute('pending.process', ['pending' => $pendingDraw->id]))
        ->assertUnprocessable()
        ->assertJsonStructure(['message']);

    Notification::assertNothingSent();
})->with('invalid participants list');

it('sends notifications in case of success', function ($participants) {
    Notification::fake();

    $pendingDraw = createPendingDraw($participants);

    ajaxGet(URL::signedRoute('pending.process', ['pending' => $pendingDraw->id]))
        ->assertSuccessful()
        ->assertJsonStructure(['message']);

    $draw = $pendingDraw->fresh()->draw;
    assertModelExists($draw);

    // Ensure Organizer receives his recap
    Notification::assertTimesSent(1, OrganizerRecap::class);
    Notification::assertSentTo(
        new AnonymousNotifiable,
        OrganizerRecap::class,
        function ($notification, $channels, $notifiable) use ($draw) {
            return $notifiable->routes['mail'] === [['name' => $draw->organizer_name, 'email' => $draw->organizer_email]];
        }
    );

    // Ensure Participants receive their own recap
    Notification::assertTimesSent(count($draw->participants), TargetDrawn::class);
    foreach($draw->participants as $participant) {
        Notification::assertSentTo($participant, TargetDrawn::class);
    }
})->with('participants list');;

it('can create draws with a non participant organizer', function ($participants) {
    Notification::fake();

    $pendingDraw = createPendingDraw($participants, [
            'participant-organizer' => '0',
            'organizer' => ['name' => 'foo', 'email' => 'foo@foobar.com'],
        ]);

    ajaxGet(URL::signedRoute('pending.process', ['pending' => $pendingDraw->id]))
        ->assertSuccessful()
        ->assertJsonStructure(['message']);

    $draw = $pendingDraw->fresh()->draw;

    assertEquals('foo', $draw->organizer_name);
    assertEquals('foo@foobar.com', $draw->organizer_email);

    // Ensure Organizer receives his recap
    Notification::assertTimesSent(1, OrganizerRecap::class);
    Notification::assertSentTo(
        new AnonymousNotifiable,
        OrganizerRecap::class,
        function ($notification, $channels, $notifiable) use ($draw) {
            return $notifiable->routes['mail'] === [['name' => $draw->organizer_name, 'email' => $draw->organizer_email]];
        }
    );

    // Ensure Participants receive their own recap
    Notification::assertTimesSent(count($draw->participants), TargetDrawn::class);
    foreach($draw->participants as $participant) {
        assertNotEquals($participant->email, $draw->organizer_email);
        Notification::assertSentTo($participant, TargetDrawn::class);
    }
})->with('participants list');

it('sends to the organizer the link to their panel', function ($participants) {
    Notification::fake();

    $pendingDraw = createPendingDraw($participants);

    ajaxGet(URL::signedRoute('pending.process', ['pending' => $pendingDraw->id]))
        ->assertSuccessful()
        ->assertJsonStructure(['message']);

    $draw = $pendingDraw->fresh()->draw;

    // Ensure Organizer receives his recap
    Notification::assertSentTo(
        new AnonymousNotifiable,
        OrganizerRecap::class,
        function ($notification, $channels, $notifiable) use ($draw) {
            return
                $notifiable->routes['mail'] === [['name' => $draw->organizer_name, 'email' => $draw->organizer_email]] &&
                $notification->toMail($notifiable)->assertSeeInHtml(
                    URL::signedRoute('organizer.index', ['draw' => $draw->hash]).'#'.base64_encode(DrawCrypt::getIV())
            );
        }
    );
})->with('participants list');

it('can deal with thousands of participants', function ($participants) {
    Notification::fake();

    $pendingDraw = createPendingDraw($participants);

    ajaxGet(URL::signedRoute('pending.process', ['pending' => $pendingDraw->id]))
        ->assertSuccessful()
        ->assertJsonStructure(['message']);

    $draw = $pendingDraw->fresh()->draw;

    assertEquals(count($participants), $draw->participants()->count());
})->with('huge participants list')->group('massive');