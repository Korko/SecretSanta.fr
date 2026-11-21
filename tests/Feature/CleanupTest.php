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

// Pruned WEEKS_BEFORE_DELETION after the event: the expired() state is any date in the past, a few days ago included
function drawToPrune(): Draw
{
    return Draw::factory()->create(['expires_at' => now()->subWeeks(Draw::WEEKS_BEFORE_DELETION)->subDay()]);
}

it('cleans up expired draws', function (): void {
    $drawNotExpired = Draw::factory()->create();
    $drawExpired = drawToPrune();

    test()->artisan('model:prune', ['--model' => [Draw::class]])->assertSuccessful();

    assertNotNull($drawNotExpired->fresh());
    assertNull($drawExpired->fresh()); // assertDeleted
});

it('cleans up everything', function (): void {
    drawToPrune();
    // TODO: need to create exclusions, dearSantas and mails

    test()->artisan('model:prune', ['--model' => [Draw::class]])->assertSuccessful();

    assertEquals(0, Draw::query()->count());
    assertEquals(0, Participant::query()->count());
    assertEquals(0, Exclusion::query()->count());
    assertEquals(0, DearSanta::query()->count());
    assertEquals(0, Mail::query()->count());
});

it('prunes failed jobs, which keep the draw secret, after a week', function (): void {
    // schedule:list loads routes/console.php like the scheduler does
    $this->artisan('schedule:list')
        ->expectsOutputToContain('queue:prune-failed --hours=168')
        ->assertSuccessful();
});
