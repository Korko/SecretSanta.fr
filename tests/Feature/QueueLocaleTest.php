<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

test('a queued job runs in the language of the request that dispatched it', function (): void {
    app()->setLocale('en');
    dispatch(function (): void {
        Cache::put('job-locale', app()->getLocale());
    })->onConnection('database');

    $payload = json_decode((string) DB::table('jobs')->value('payload'), true);
    expect(data_get($payload, 'data.locale'))->toBe('en');

    // The worker starts in another language
    app()->setLocale('fr');
    Artisan::call('queue:work', ['connection' => 'database', '--once' => true]);

    expect(Cache::get('job-locale'))->toBe('en');
});
