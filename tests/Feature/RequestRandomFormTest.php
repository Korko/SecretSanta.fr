<?php

use App\Channels\MailChannel;
use App\Models\Draw;
use App\Models\Participant;
use App\Notifications\OrganizerRecap;
use App\Notifications\TargetDrawn;
use Illuminate\Notifications\AnonymousNotifiable;

function createDraw($participants, $params = []) {
    return ajaxPost('/', $params + [
            'participant-organizer' => '1',
            'participants'          => $participants,
            'title'                 => 'this is a test',
            'content'               => 'test mail {SANTA} => {TARGET}',
        ])
        ->assertJsonStructure(['message']);
}

it('sends no notifications in case of error', function ($participants) {
    Notification::fake();

    assertEquals(0, Draw::count());
    assertEquals(0, Participant::count());

    createDraw($participants)
        ->assertStatus(422);

    assertEquals(0, Draw::count());
    assertEquals(0, Participant::count());

    Notification::assertNothingSent();
})->with('invalid participants list');

it('can create draws', function ($participants) {
    Notification::fake();

    assertEquals(0, Draw::count());
    assertEquals(0, Participant::count());

    createDraw($participants)
        ->assertSuccessful();

    assertEquals(1, Draw::count());
    assertEquals(3, Participant::count());
})->with('participants list');;

it('sends notifications in case of success', function ($participants) {
    Notification::fake();

    createDraw($participants)
        ->assertSuccessful();

    $draw = Draw::find(1);

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

    createDraw($participants, [
            'participant-organizer' => '0',
            'organizer' => ['name' => 'foo', 'email' => 'foo@foobar.com'],
        ])
        ->assertSuccessful();

    $draw = Draw::find(1);
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

    createDraw($participants)
        ->assertSuccessful();

    $draw = Draw::find(1);

    // Ensure Organizer receives his recap
    Notification::assertSentTo(
        new AnonymousNotifiable,
        OrganizerRecap::class,
        function ($notification, $channels, $notifiable) use ($draw) {
            return
                $notifiable->routes['mail'] === [['name' => $draw->organizer_name, 'email' => $draw->organizer_email]] &&
                $notification->toMail($notifiable)->assertSeeInHtml(
                    URL::signedRoute('organizerPanel', ['draw' => $draw->hash]).'#'.base64_encode(DrawCrypt::getIV())
            );
        }
    );
})->with('participants list');

it('can deal with thousands of participants', function ($participants) {
    Notification::fake();

    assertEquals(0, Draw::count());
    assertEquals(0, Participant::count());

    createDraw($participants)
        ->assertSuccessful();

    assertEquals(1, Draw::count());
    assertEquals($participants[0]['name'], Draw::find(1)->organizer_name);
    assertEquals(count($participants), Participant::count());
})->with('huge participants list')->group('massive');