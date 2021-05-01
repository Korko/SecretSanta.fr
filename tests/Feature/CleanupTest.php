<?php

namespace Tests\Feature;

use App\Models\Draw;
use App\Models\Participant;
use App\Models\Exclusion;
use App\Models\DearSanta;
use App\Models\Mail;

it('cleans up expired draws', function () {
	$drawNotExpired = Draw::factory()->create();
    $drawExpired = Draw::factory()->expired()->create();

    Draw::cleanup();

    assertNotNull($drawNotExpired->fresh());
    assertNull($drawExpired->fresh());// assertDeleted
});

it('cleans up everything', function () {
    Draw::factory()->expired()->create();
// TODO: need to create exclusions, dearsantas and mails
    Draw::cleanup();

    assertEquals(0, Draw::count());
    assertEquals(0, Participant::count());
    assertEquals(0, Exclusion::count());
    assertEquals(0, DearSanta::count());
    assertEquals(0, Mail::count());
});