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
        // idx (array offset, starting at 0) = id (database offset, starting at 1) - 1
        assertEquals($participants[$targets[$idx]]['name'], $draw->participants[$draw->participants[$idx]->target_id - 1]->name);
        assertEquals($participants[$targets[$idx]]['name'], $draw->participants[$idx]->target->name);

        assertEquals($participant['name'], $draw->participants[$draw->participants[$idx]->target_id - 1]->santa->name);
        assertEquals($participant['name'], $draw->participants[$idx]->target->santa->name);
    }
})->with('unique participants list');
