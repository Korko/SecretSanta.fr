<?php

declare(strict_types=1);

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
    $drawExpired = Draw::factory()->prunable()->create();

    test()->artisan('model:prune', ['--model' => [Draw::class]])->assertSuccessful();

    assertNotNull($drawNotExpired->fresh());
    assertNull($drawExpired->fresh()); // assertDeleted
});

it('cleans up everything', function (): void {
    Draw::factory()->prunable()->create();
    // TODO: need to create exclusions, dearSantas and mails

    test()->artisan('model:prune', ['--model' => [Draw::class]])->assertSuccessful();

    assertEquals(0, Draw::query()->count());
    assertEquals(0, Participant::query()->count());
    assertEquals(0, Exclusion::query()->count());
    assertEquals(0, DearSanta::query()->count());
    assertEquals(0, Mail::query()->count());
});

it('keeps the data one month after the event', function (): void {
    $this->travelTo('2026-12-24 10:00:00');
    $draw = Draw::factory()->create(['expires_at' => '2026-12-24']);

    expect($draw->deleted_at->toDateString())->toBe('2027-01-24');

    $this->travelTo('2027-01-23 23:59:00');
    test()->artisan('model:prune', ['--model' => [Draw::class]])->assertSuccessful();
    assertNotNull($draw->fresh());

    $this->travelTo('2027-01-24 00:00:00');
    test()->artisan('model:prune', ['--model' => [Draw::class]])->assertSuccessful();
    assertNull($draw->fresh());
});

it('keeps a draw of the end of a month until the end of the next one', function (): void {
    $this->travelTo('2027-01-31 10:00:00');
    $draw = Draw::factory()->create(['expires_at' => '2027-01-31']);

    // No overflow into March
    expect($draw->deleted_at->toDateString())->toBe('2027-02-28');
});
