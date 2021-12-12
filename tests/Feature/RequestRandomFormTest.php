<?php

use App\Channels\MailChannel;
use App\Mail\OrganizerRecap as OrganizerRecapMail;
use App\Models\Draw;
use App\Models\Participant;
use App\Notifications\OrganizerRecap as OrganizerRecapNotif;
use App\Notifications\TargetDrawn;
use Illuminate\Notifications\AnonymousNotifiable;

it('sends no notifications in case of error', function ($participants) {
    Notification::fake();

    assertEquals(0, Draw::count());
    assertEquals(0, Participant::count());

    ajaxPost('/', [
            'participant-organizer' => '1',
            'participants'          => $participants,
            'title'                 => 'this is a test',
            'content-email'         => 'test mail {SANTA} => {TARGET}',
            'data-expiration'       => date('Y-m-d', strtotime('+2 days')),
        ])
        ->assertStatus(422)
        ->assertJsonStructure(['message']);

    assertEquals(0, Draw::count());
    assertEquals(0, Participant::count());

    Notification::assertNothingSent();
})->with('invalid participants list');

it('can create draws', function () {
    assertEquals(0, Draw::count());
    assertEquals(0, Participant::count());

    $participants = generateParticipants(3);

    ajaxPost('/', [
            'participant-organizer' => '1',
            'participants'          => $participants,
            'title'                 => 'this is a test',
            'content-email'         => 'test mail {SANTA} => {TARGET}',
            'data-expiration'       => date('Y-m-d', strtotime('+2 days')),
        ])
        ->assertSuccessful()
        ->assertJsonStructure(['message']);

    assertEquals(1, Draw::count());
    assertEquals(3, Participant::count());
});

it('sends notifications in case of success', function () {
    Notification::fake();

    ajaxPost('/', [
            'participant-organizer' => '1',
            'participants'          => generateParticipants(3),
            'title'                 => 'this is a test',
            'content-email'         => 'test mail {SANTA} => {TARGET}',
            'data-expiration'       => date('Y-m-d', strtotime('+2 days')),
        ])
        ->assertSuccessful()
        ->assertJsonStructure(['message']);

    $draw = Draw::find(1);

    // Ensure Organizer receives his recap
    Notification::assertTimesSent(1, OrganizerRecapNotif::class);
    Notification::assertSentTo(
        new AnonymousNotifiable,
        OrganizerRecapNotif::class,
        function ($notification, $channels, $notifiable) use ($draw) {
            return $notifiable->routes['mail'] === [$draw->organizer_email => $draw->organizer_name];
        }
    );

    // Ensure Participants receive their own recap
    Notification::assertTimesSent(count($draw->participants), TargetDrawn::class);
    foreach($draw->participants as $participant) {
        Notification::assertSentTo($participant, TargetDrawn::class);
    }
});

it('can create draws with a non participant organizer', function () {
    Notification::fake();

    $participants = generateParticipants(3);

    ajaxPost('/', [
            'participant-organizer' => '0',
            'organizer'             => ['name' => 'foo', 'email' => 'foo@foobar.com'],
            'participants'          => $participants,
            'title'                 => 'this is a test',
            'content-email'         => 'test mail {SANTA} => {TARGET}',
            'data-expiration'       => date('Y-m-d', strtotime('+2 days')),
        ])
        ->assertSuccessful()
        ->assertJsonStructure(['message']);

    $draw = Draw::find(1);
    assertEquals('foo', $draw->organizer_name);
    assertEquals('foo@foobar.com', $draw->organizer_email);

    // Ensure Organizer receives his recap
    Notification::assertTimesSent(1, OrganizerRecapNotif::class);
    Notification::assertSentTo(
        new AnonymousNotifiable,
        OrganizerRecapNotif::class,
        function ($notification, $channels, $notifiable) use ($draw) {
            return $notifiable->routes['mail'] === [$draw->organizer_email => $draw->organizer_name];
        }
    );

    // Ensure Participants receive their own recap
    Notification::assertTimesSent(count($draw->participants), TargetDrawn::class);
    foreach($draw->participants as $participant) {
        assertNotEquals($participant->email, $draw->organizer_email);
        Notification::assertSentTo($participant, TargetDrawn::class);
    }
});

it('sends to the organizer the link to their panel', function () {
    Notification::fake();

    ajaxPost('/', [
            'participant-organizer' => '1',
            'participants'          => generateParticipants(3),
            'title'                 => 'this is a test',
            'content-email'         => 'test mail {SANTA} => {TARGET}',
            'data-expiration'       => date('Y-m-d', strtotime('+2 days')),
        ])
        ->assertSuccessful()
        ->assertJsonStructure(['message']);

    $draw = Draw::find(1);

    // Ensure Organizer receives his recap
    Notification::assertSentTo(
        new AnonymousNotifiable,
        OrganizerRecapNotif::class,
        function ($notification, $channels, $notifiable) use ($draw) {
            $link = $notification->toMail($notifiable)->data()['panelLink'];

            // Check the recap link is valid
            test()->get($link)->assertSuccessful();

            // Check link can be used for support
            assertEquals($draw->id, URLParser::parseByName('organizerPanel', $link)->draw->id);

            return $notifiable->routes['mail'] === [$draw->organizer_email => $draw->organizer_name];
        }
    );
});

it('can deal with thousands of participants', function () {
    assertEquals(0, Draw::count());
    assertEquals(0, Participant::count());

    $totalParticipants = 400;
    $participants = generateParticipants($totalParticipants, false);

    ajaxPost('/', [
            'participant-organizer' => '1',
            'participants'          => $participants,
            'title'                 => 'this is a test',
            'content-email'         => 'test mail {SANTA} => {TARGET}',
            'data-expiration'       => date('Y-m-d', strtotime('+2 days')),
        ])
        ->assertSuccessful()
        ->assertJsonStructure(['message']);

    assertEquals(1, Draw::count());
    assertEquals($participants[0]['name'], Draw::find(1)->organizer_name);
    assertEquals($totalParticipants, Participant::count());
})->group('massive');
