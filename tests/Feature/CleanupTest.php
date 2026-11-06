<?php

namespace Tests\Feature;

use App\Models\DearSanta;
use App\Models\Draw;
use App\Models\Exclusion;
use App\Models\Mail;
use App\Models\Participant;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertNull;

it('cleans up expired draws', function (): void {
    $drawNotExpired = Draw::factory()->create();
    $drawExpired = Draw::factory()->expired()->create();

    test()->artisan('model:prune', ['--model' => [Draw::class]])->assertSuccessful();

    assertNotNull($drawNotExpired->fresh());
    assertNull($drawExpired->fresh()); // assertDeleted
});

it('cleans up everything', function (): void {
    Draw::factory()->expired()->create();
    // TODO: need to create exclusions, dearSantas and mails

    test()->artisan('model:prune', ['--model' => [Draw::class]])->assertSuccessful();

    assertEquals(0, Draw::query()->count());
    assertEquals(0, Participant::query()->count());
    assertEquals(0, Exclusion::query()->count());
    assertEquals(0, DearSanta::query()->count());
    assertEquals(0, Mail::query()->count());
});
