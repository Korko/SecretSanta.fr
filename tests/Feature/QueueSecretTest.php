<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

test('a failed job is not stored, nor its draw secret', function (): void {
    $secret = base64_encode(DrawCrypt::getIV());
    dispatch(function (): void {
        throw new RuntimeException('Mail server down');
    })->onConnection('database');

    // Needed by the worker until the job is done
    $pending = json_decode((string) DB::table('jobs')->value('payload'), true);
    expect(data_get($pending, 'data.iv'))->toBe($secret);

    Artisan::call('queue:work', ['connection' => 'database', '--once' => true]);

    expect(DB::table('jobs')->count())->toBe(0)
        ->and(resolve('queue.failer')->all())->toBe([])
        ->and(Schema::hasTable('failed_jobs'))->toBeFalse();
});

test('a job without the draw secret runs with a new one, not the one left by the previous job', function (): void {
    $secret = base64_encode(DrawCrypt::getIV());
    dispatch(function (): void {
        Cache::put('job-secret', base64_encode(DrawCrypt::getIV()));
    })->onConnection('database');

    $payload = json_decode((string) DB::table('jobs')->value('payload'), true);
    unset($payload['data']['iv']);
    DB::table('jobs')->update(['payload' => json_encode($payload)]);

    // Same process: the worker still holds the secret of the request that dispatched the job
    Artisan::call('queue:work', ['connection' => 'database', '--once' => true]);

    expect(Cache::get('job-secret'))->toBeString()->not->toBe($secret);
});
