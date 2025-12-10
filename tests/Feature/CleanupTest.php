<?php

namespace Tests\Feature;

use App\Models\DearSanta;
use App\Models\Draw;
use App\Models\Exclusion;
use App\Models\Mail;
use App\Models\Participant;

it('cleans up expired draws', function () {
    $drawNotExpired = Draw::factory()->create();
    $drawExpired = Draw::factory()->expired()->create();

    test()->artisan('model:prune', ['--model' => [Draw::class]])->assertSuccessful();

    assertNotNull($drawNotExpired->fresh());
    assertNull($drawExpired->fresh()); // assertDeleted
});

it('cleans up everything', function () {
    Draw::factory()->expired()->create();
    // TODO: need to create exclusions, dearSantas and mails

    test()->artisan('model:prune', ['--model' => [Draw::class]])->assertSuccessful();

    assertEquals(0, Draw::count());
    assertEquals(0, Participant::count());
    assertEquals(0, Exclusion::count());
    assertEquals(0, DearSanta::count());
    assertEquals(0, Mail::count());
});
