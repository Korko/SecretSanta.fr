<?php

use App\Channels\MailChannel;
use App\Models\Draw;
use App\Models\Participant;
use App\Notifications\OrganizerRecap;
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
            'content'               => 'test mail {SANTA} => {TARGET}',
            'data-expiration'       => date('Y-m-d', strtotime('+2 days')),
        ])
        ->assertStatus(422)
        ->assertJsonStructure(['message']);

    assertEquals(0, Draw::count());
    assertEquals(0, Participant::count());

    Notification::assertNothingSent();
})->with('invalid participants list');

it('can create draws', function () {
    Notification::fake();

    assertEquals(0, Draw::count());
    assertEquals(0, Participant::count());

    $participants = generateParticipants(3);

    ajaxPost('/', [
            'participant-organizer' => '1',
            'participants'          => $participants,
            'title'                 => 'this is a test',
            'content'               => 'test mail {SANTA} => {TARGET}',
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
            'content'               => 'test mail {SANTA} => {TARGET}',
            'data-expiration'       => date('Y-m-d', strtotime('+2 days')),
        ])
        ->assertSuccessful()
        ->assertJsonStructure(['message']);

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
});

it('can create draws with a non participant organizer', function () {
    Notification::fake();

    $participants = generateParticipants(3);

    ajaxPost('/', [
            'participant-organizer' => '0',
            'organizer'             => ['name' => 'foo', 'email' => 'foo@foobar.com'],
            'participants'          => $participants,
            'title'                 => 'this is a test',
            'content'               => 'test mail {SANTA} => {TARGET}',
            'data-expiration'       => date('Y-m-d', strtotime('+2 days')),
        ])
        ->assertSuccessful()
        ->assertJsonStructure(['message']);

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
});

it('sends to the organizer the link to their panel', function () {
    Notification::fake();

    ajaxPost('/', [
            'participant-organizer' => '1',
            'participants'          => generateParticipants(3),
            'title'                 => 'this is a test',
            'content'               => 'test mail {SANTA} => {TARGET}',
            'data-expiration'       => date('Y-m-d', strtotime('+2 days')),
        ])
        ->assertSuccessful()
        ->assertJsonStructure(['message']);

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
});

it('can deal with thousands of participants', function () {
    Notification::fake();

    assertEquals(0, Draw::count());
    assertEquals(0, Participant::count());

    $totalParticipants = 400;
    $participants = generateParticipants($totalParticipants, false);

    ajaxPost('/', [
            'participant-organizer' => '1',
            'participants'          => $participants,
            'title'                 => 'this is a test',
            'content'               => 'test mail {SANTA} => {TARGET}',
            'data-expiration'       => date('Y-m-d', strtotime('+2 days')),
        ])
        ->assertSuccessful()
        ->assertJsonStructure(['message']);

    assertEquals(1, Draw::count());
    assertEquals($participants[0]['name'], Draw::find(1)->organizer_name);
    assertEquals($totalParticipants, Participant::count());
})->group('massive');