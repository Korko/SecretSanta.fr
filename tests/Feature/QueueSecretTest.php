<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

test('a failed job does not keep the draw secret', function (): void {
    $secret = base64_encode(DrawCrypt::getIV());
    dispatch(function (): void {
        throw new RuntimeException('Mail server down');
    })->onConnection('database');

    // Needed by the worker until the job is done
    $pending = json_decode((string) DB::table('jobs')->value('payload'), true);
    expect(data_get($pending, 'data.iv'))->toBe($secret);

    Artisan::call('queue:work', ['connection' => 'database', '--once' => true]);

    $failed = DB::table('failed_jobs')->first();
    expect($failed)->not->toBeNull()
        ->and(json_decode((string) $failed->payload, true))->not->toHaveKey('data.iv')->toHaveKey('data.command')
        ->and((string) $failed->exception)->toContain('Mail server down');
});

test('a retried failed job fails right away, without running nor using its tries', function (): void {
    dispatch(function (): void {
        Cache::increment('runs');
        throw new RuntimeException('Mail server down');
    })->onConnection('database');

    Artisan::call('queue:work', ['connection' => 'database', '--once' => true]);
    Artisan::call('queue:retry', ['id' => ['all']]);
    // Same worker, which still holds the secret of the first attempt, and tries left
    Artisan::call('queue:work', ['connection' => 'database', '--once' => true, '--tries' => 3]);

    expect(Cache::get('runs'))->toBe(1)
        ->and(DB::table('jobs')->count())->toBe(0)
        ->and((string) DB::table('failed_jobs')->value('exception'))->toContain('No draw secret');
});

test('a job cannot be dispatched without a draw secret', function (): void {
    DrawCrypt::setIV(null);

    expect(fn () => dispatch(function (): void {})->onConnection('database'))->toThrow(RuntimeException::class, 'No draw secret')
        ->and(DB::table('jobs')->count())->toBe(0);
});
