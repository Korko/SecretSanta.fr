<?php

use App\Models\Draw;
use App\Models\Exclusion;
use App\Models\Participant;

it('records new entries in case of success', function ($participants) {
    assertEquals(0, Draw::count());
    assertEquals(0, Participant::count());
    assertEquals(0, Exclusion::count());

    $draw = createServiceDraw($participants);

    $exclusions = array_reduce($participants, function ($sum, $participant) {
        return $sum + count($participant['exclusions']);
    });

    assertEquals(1, Draw::count());
    assertTrue($draw->is(Draw::find(1)));

    assertEquals(count($participants), Participant::count());
    assertEquals($exclusions, Exclusion::count());

    // Carreful, array is 0..n, Db is 1..n
    foreach ($participants as $idx => $participant) {
        assertEquals($participant['name'], Participant::find($idx + 1)->name);
        assertEquals($participant['email'], Participant::find($idx + 1)->email);
    }
})->with('valid participants list');

it('saves the correct target', function ($participants, $targets) {
    $draw = createServiceDraw($participants);

    foreach ($participants as $idx => $participant) {
        assertEquals($participants[$targets[$idx]]['name'], $draw->participants[$idx]->target->name);
        assertEquals($participant['name'], $draw->participants[$idx]->target->santa->name);
    }
})->with('unique participants list');

it('can handle several exclusions', function () {
    $participants = [
        ['name' => uniqid(), 'email' => 'test@test.com', 'exclusions' => [11, 22, 16, 18, 8, 9, 5, 4, 2, 1, 3, 7, 12, 23, 21, 20, 19, 14]],
        ['name' => uniqid(), 'email' => 'test@test.com', 'exclusions' => []],
        ['name' => uniqid(), 'email' => 'test@test.com', 'exclusions' => []],
        ['name' => uniqid(), 'email' => 'test@test.com', 'exclusions' => []],
        ['name' => uniqid(), 'email' => 'test@test.com', 'exclusions' => []],
        ['name' => uniqid(), 'email' => 'test@test.com', 'exclusions' => []],
        ['name' => uniqid(), 'email' => 'test@test.com', 'exclusions' => []],
        ['name' => uniqid(), 'email' => 'test@test.com', 'exclusions' => [11]],
        ['name' => uniqid(), 'email' => 'test@test.com', 'exclusions' => []],
        ['name' => uniqid(), 'email' => 'test@test.com', 'exclusions' => []],
        ['name' => uniqid(), 'email' => 'test@test.com', 'exclusions' => []],
        ['name' => uniqid(), 'email' => 'test@test.com', 'exclusions' => []],
        ['name' => uniqid(), 'email' => 'test@test.com', 'exclusions' => []],
        ['name' => uniqid(), 'email' => 'test@test.com', 'exclusions' => []],
        ['name' => uniqid(), 'email' => 'test@test.com', 'exclusions' => [11]],
        ['name' => uniqid(), 'email' => 'test@test.com', 'exclusions' => []],
        ['name' => uniqid(), 'email' => 'test@test.com', 'exclusions' => []],
        ['name' => uniqid(), 'email' => 'test@test.com', 'exclusions' => []],
        ['name' => uniqid(), 'email' => 'test@test.com', 'exclusions' => []],
        ['name' => uniqid(), 'email' => 'test@test.com', 'exclusions' => []],
        ['name' => uniqid(), 'email' => 'test@test.com', 'exclusions' => []],
        ['name' => uniqid(), 'email' => 'test@test.com', 'exclusions' => [1, 2, 3, 4, 5, 6, 7, 8, 23, 20, 19, 18, 16, 15, 14, 13, 12, 11, 10, 17, 9, 22]],
        ['name' => uniqid(), 'email' => 'test@test.com', 'exclusions' => []],
        ['name' => uniqid(), 'email' => 'test@test.com', 'exclusions' => []],
    ];

    $this->assertNotEquals(null, createServiceDraw($participants));
});
