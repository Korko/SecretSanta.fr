<?php

use App\Mail\OrganizerRecap as OrganizerRecapMail;
use App\Models\Draw;
use App\Models\Participant;
use App\Notifications\OrganizerRecap as OrganizerRecapNotif;
use App\Notifications\TargetDrawn;

it('sends no notifications in case of error', function ($participants) {
    Notification::fake();
    Notification::assertNothingSent();

    assertEquals(0, Draw::count());
    assertEquals(0, Participant::count());

    ajaxPost('/', [
            'participants'    => $participants,
            'title'           => 'this is a test',
            'content-email'   => 'test mail {SANTA} => {TARGET}',
            'data-expiration' => date('Y-m-d', strtotime('+2 days')),
        ])
        ->assertJsonStructure(['message'])
        ->assertStatus(422);

    assertEquals(0, Draw::count());
    assertEquals(0, Participant::count());
})->with('invalid participants list');

it('sends notifications in case of success', function () {
    Notification::fake();

    assertEquals(0, Draw::count());
    assertEquals(0, Participant::count());

    $participants = createAjaxDraw(3);

    assertEquals(1, Draw::count());
    assertEquals(3, Participant::count());

    $draw = Draw::find(1);

    // Ensure Organizer receives his recap
    Notification::assertTimesSent(1, OrganizerRecapNotif::class);
    Notification::assertSentTo($draw->organizer, OrganizerRecapNotif::class);

    // Ensure Participants receive their own recap
    Notification::assertTimesSent(count($participants), TargetDrawn::class);
    foreach($draw->participants as $participant) {
        Notification::assertSentTo($participant, TargetDrawn::class);
    }
});

test('notification contains title and body specified by organizer');

it('sends to the organizer the link to their panel', function () {
    Mail::fake();

    $participants = createAjaxDraw(3);

    // Ensure Organizer receives his recap
    assertHasMailPushed(OrganizerRecapMail::class, $participants[0]['email'], function ($m) use (&$link) {
        $link = $m->panelLink;
    });

    $draw = URLParser::parseByName('organizerPanel', $link)->draw;

    assertNotEquals(0, $draw->participants->count());

    foreach ($draw->participants as $participant) {
        assertContains($participant->name, array_column($participants, 'name'));
        assertContains($participant->email, array_column($participants, 'email'));
    }
});