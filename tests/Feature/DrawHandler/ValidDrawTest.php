<?php

use App\Models\Draw;
use App\Models\Exclusion;
use App\Models\Participant;

it('records new entries in case of success', function ($participants) {
    assertEquals(0, Draw::count());
    assertEquals(0, Participant::count());
    assertEquals(0, Exclusion::count());

    $draw = createServiceDraw($participants);

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

it('saves the correct target', function ($participants, $targets) {
    $draw = createServiceDraw($participants);

    foreach($participants as $idx => $participant) {
        assertEquals($participants[$targets[$idx]]['name'], $draw->participants[$idx]->target->name);
    }
})->with('unique participants list');
