<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\DearSanta;
use App\Models\Draw;
use App\Models\Exclusion;
use App\Models\Mail;
use App\Models\Participant;
use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\Schedule;

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

it('prunes failed jobs, which keep the draw secret, after a week', function (): void {
    $prune = collect(resolve(Schedule::class)->events())
        ->first(fn (Event $event): bool => str_contains((string) $event->command, 'queue:prune-failed'));

    expect($prune)->not->toBeNull()
        ->and($prune->command)->toContain('--hours=168')
        ->and($prune->expression)->toBe('0 0 * * *');
});
