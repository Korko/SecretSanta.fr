<?php

use App\Mail\OrganizerRecap as OrganizerRecapMail;
use App\Models\Draw;
use App\Models\Participant;
use App\Notifications\OrganizerRecap as OrganizerRecapNotif;
use App\Notifications\TargetDrawn;

it('sends no notifications in case of error', function ($participants) {
    Notification::fake();

    assertEquals(0, Draw::count());
    assertEquals(0, Participant::count());

    ajaxPost('/', [
            'participants'    => $participants,
            'title'           => 'this is a test',
            'content-email'   => 'test mail {SANTA} => {TARGET}',
            'data-expiration' => date('Y-m-d', strtotime('+2 days')),
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
            'participants'    => $participants,
            'title'           => 'this is a test',
            'content-email'   => 'test mail {SANTA} => {TARGET}',
            'data-expiration' => date('Y-m-d', strtotime('+2 days')),
        ])
        ->assertSuccessful()
        ->assertJsonStructure(['message']);

    assertEquals(1, Draw::count());
    assertEquals(3, Participant::count());
});

it('sends notifications in case of success', function () {
    Notification::fake();

    ajaxPost('/', [
            'participants'    => generateParticipants(3),
            'title'           => 'this is a test',
            'content-email'   => 'test mail {SANTA} => {TARGET}',
            'data-expiration' => date('Y-m-d', strtotime('+2 days')),
        ])
        ->assertSuccessful()
        ->assertJsonStructure(['message']);

    $draw = Draw::find(1);

    // Ensure Organizer receives his recap
    Notification::assertTimesSent(1, OrganizerRecapNotif::class);
    Notification::assertSentTo($draw->organizer, OrganizerRecapNotif::class);

    // Ensure Participants receive their own recap
    Notification::assertTimesSent(count($draw->participants), TargetDrawn::class);
    foreach($draw->participants as $participant) {
        Notification::assertSentTo($participant, TargetDrawn::class);
    }
});

it('sends to the organizer the link to their panel', function () {
    Notification::fake();

    ajaxPost('/', [
            'participants'    => generateParticipants(3),
            'title'           => 'this is a test',
            'content-email'   => 'test mail {SANTA} => {TARGET}',
            'data-expiration' => date('Y-m-d', strtotime('+2 days')),
        ])
        ->assertSuccessful()
        ->assertJsonStructure(['message']);

    $draw = Draw::find(1);

    // Ensure Organizer receives his recap
    Notification::assertSentTo($draw->organizer, function (OrganizerRecapNotif $notification) use ($draw) {
        $link = $notification->toMail($draw->organizer)->data()['panelLink'];

        // Check the recap link is valid
        test()->get($link)->assertSuccessful();

        // Check link can be used for support
        assertEquals($draw->id, URLParser::parseByName('organizerPanel', $link)->draw->id);

        return true;
    });
});