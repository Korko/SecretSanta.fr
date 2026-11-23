<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\DearSanta;
use App\Models\Draw;
use App\Models\Exclusion;
use App\Models\Mail;
use App\Models\Participant;
use Illuminate\Support\Facades\DB;

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

it('prunes failed jobs after a week', function (): void {
    // schedule:list loads routes/console.php like the scheduler does
    $this->artisan('schedule:list')
        ->expectsOutputToContain('queue:prune-failed --hours=168')
        ->assertSuccessful();

    foreach (['old' => now()->subDays(8), 'recent' => now()->subDays(6)] as $uuid => $failedAt) {
        DB::table('failed_jobs')->insert([
            'uuid' => $uuid, 'connection' => 'database', 'queue' => 'default',
            'payload' => '{}', 'exception' => 'Mail server down', 'failed_at' => $failedAt,
        ]);
    }

    $this->artisan('queue:prune-failed', ['--hours' => 168])->assertSuccessful();

    assertEquals(['recent'], DB::table('failed_jobs')->pluck('uuid')->all());
});
