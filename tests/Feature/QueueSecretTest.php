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

test('a retried failed job runs without the draw secret, not even the one left by the previous job', function (): void {
    $secret = base64_encode(DrawCrypt::getIV());
    dispatch(function (): void {
        if (! Cache::has('failed-once')) {
            Cache::put('failed-once', true);
            throw new RuntimeException('Mail server down');
        }

        Cache::put('job-secret', base64_encode(DrawCrypt::getIV()));
    })->onConnection('database');

    Artisan::call('queue:work', ['connection' => 'database', '--once' => true]);
    Artisan::call('queue:retry', ['id' => ['all']]);
    // Same worker: it still holds the secret of the first attempt
    Artisan::call('queue:work', ['connection' => 'database', '--once' => true]);

    expect(Cache::get('job-secret'))->toBeString()->not->toBe($secret);
});
