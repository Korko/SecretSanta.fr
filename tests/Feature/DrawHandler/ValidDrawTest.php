<?php

use App\Mail\OrganizerRecap as OrganizerRecapMail;
use App\Notifications\OrganizerRecap as OrganizerRecapNotif;
use App\Notifications\TargetDrawn;
use App\Models\Draw;
use App\Models\Exclusion;
use App\Models\Participant;

it('records new entries in case of success', function ($participants) {
    assertEquals(0, Draw::count());
    assertEquals(0, Participant::count());
    assertEquals(0, Exclusion::count());

    $draw = createDraw($participants);

    $exclusions = array_reduce($participants, function ($sum, $participant) { return $sum + count($participant['exclusions']); });

    assertEquals(1, Draw::count());
    assertTrue($draw->is(Draw::find(1)));

    assertEquals(count($participants), Participant::count());
    assertEquals($exclusions, Exclusion::count());

    // Carreful, array is 0..n, Db is 1..n
    foreach($participants as $idx => $participant) {
        assertEquals($participant['name'], Participant::find($idx + 1)->name);
        assertEquals($participant['email'], Participant::find($idx + 1)->email);
    }
})->with('valid participants list');

it('sends notifications in case of success', function ($participants) {
    Notification::fake();

    $draw = createDraw($participants);

    // Ensure Organizer receives his recap
    Notification::assertTimesSent(1, OrganizerRecapNotif::class);
    Notification::assertSentTo($draw->organizer, OrganizerRecapNotif::class);

    // Ensure Participants receive their own recap
    Notification::assertTimesSent(count($participants), TargetDrawn::class);
    foreach($draw->participants as $participant) {
        Notification::assertSentTo($participant, TargetDrawn::class);
    }
})->with('valid participants list');

it('saves the correct target', function ($participants, $targets) {
    $draw = createDraw($participants);

    foreach($participants as $idx => $participant) {
        assertEquals($participants[$targets[$idx]]['name'], $draw->participants[$idx]->target->name);
    }
})->with('unique participants list');

//TODO test mail title

it('sends to the organizer the link to their panel', function ($participants) {
    Mail::fake();

    createDraw($participants);

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
})->with('valid participants list');
