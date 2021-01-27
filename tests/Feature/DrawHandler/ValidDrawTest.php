<?php

use App\Mail\OrganizerRecap;
use App\Mail\TargetDrawn;
use App\Models\Draw;
use App\Models\Exclusion;
use App\Models\Participant;

function createDraw ($participants) {
    DrawHandler::toParticipants($participants)
        ->expiresAt(date('Y-m-d', strtotime('+2 days')))
        ->sendMail('test mail {SANTA} => {TARGET} title', 'test mail {SANTA} => {TARGET} body');
}

it('records new entries in case of success', function ($participants) {
    assertEquals(0, Draw::count());
    assertEquals(0, Participant::count());
    assertEquals(0, Exclusion::count());

    createDraw($participants);

    $exclusions = array_reduce($participants, function ($sum, $participant) { return $sum + count($participant['exclusions']); });

    assertEquals(1, Draw::count());
    assertEquals(count($participants), Participant::count());
    assertEquals($exclusions, Exclusion::count());

    // Carreful, array is 0..n, Db is 1..n
    foreach($participants as $idx => $participant) {
        assertEquals($participant['name'], Participant::find($idx + 1)->name);
    }
})->with('valid participants list');

it('sends emails in case of success', function ($participants) {
    Mail::fake();

    createDraw($participants);

    // Ensure Organizer receives his recap
    assertHasMailPushed(OrganizerRecap::class, $participants[0]['email']);

    // Ensure Participants receive their own recap
    foreach($participants as $participant) {
        assertHasMailPushed(TargetDrawn::class, $participant['email']);
    }
})->with('valid participants list');

it('sends target name in emails', function ($participants, $targets) {
    Mail::fake();

    createDraw($participants);

    // TODO: assert body
    $title = null;
    foreach($participants as $idx => $participant) {
        assertHasMailPushed(TargetDrawn::class, $participant['email'], function ($m) use (&$title) {
            $title = $m->subject;
        });
        assertStringContainsString('test mail '.$participant['name'].' => '.$participants[$targets[$idx]]['name'].' title', html_entity_decode($title));
    }
})->with('unique participants list');

it('stores database crypted and uncryptable entries', function ($participants) {
    Mail::fake();

    createDraw($participants);

    // Ensure Organizer receives his recap
    assertHasMailPushed(OrganizerRecap::class, 'test@test.com', function ($m) use (&$link) {
        $link = $m->panelLink;
    });

    $draw = URLParser::parseByName('organizerPanel', $link)->draw;

    assertNotEquals(0, $draw->participants->count());

    foreach ($draw->participants as $participant) {
        assertContains($participant->name, array_column($participants, 'name'));
        assertContains($participant->email, array_column($participants, 'email'));
    }
})->with('valid participants list');
