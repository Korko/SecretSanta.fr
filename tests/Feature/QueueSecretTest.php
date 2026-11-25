<?php

declare(strict_types=1);

use App\Contracts\WithDrawSecret;
use App\Models\Draw;
use App\Notifications\TargetDrawn;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

final class FailingJobWithDrawSecret implements ShouldQueue, WithDrawSecret
{
    use Queueable;

    public function handle(): void
    {
        throw new RuntimeException('Mail server down');
    }
}

final class CachingJobWithDrawSecret implements ShouldQueue, WithDrawSecret
{
    use Queueable;

    public function handle(): void
    {
        Cache::put('job-secret', base64_encode(DrawCrypt::getSecret()));
    }
}

test('a failed job is not stored, nor its draw secret', function (): void {
    $secret = base64_encode(DrawCrypt::getSecret());
    dispatch(new FailingJobWithDrawSecret)->onConnection('database');

    // Needed by the worker until the job is done
    $pending = json_decode((string) DB::table('jobs')->value('payload'), true);
    expect(data_get($pending, 'data.secret'))->toBe($secret);

    Artisan::call('queue:work', ['connection' => 'database', '--once' => true]);

    expect(DB::table('jobs')->count())->toBe(0)
        ->and(resolve('queue.failer')->all())->toBe([])
        ->and(Schema::hasTable('failed_jobs'))->toBeFalse();
});

test('a job without the draw secret runs with a new one, not the one left by the previous job', function (): void {
    $secret = base64_encode(DrawCrypt::getSecret());
    dispatch(function (): void {
        Cache::put('job-secret', base64_encode(DrawCrypt::getSecret()));
    })->onConnection('database');

    $payload = json_decode((string) DB::table('jobs')->value('payload'), true);
    unset($payload['data']['secret']);
    DB::table('jobs')->update(['payload' => json_encode($payload)]);

    // Same process: the worker still holds the secret of the request that dispatched the job
    Artisan::call('queue:work', ['connection' => 'database', '--once' => true]);

    expect(Cache::get('job-secret'))->toBeString()->not->toBe($secret);
});

test('a job queued before the rename still runs with its draw secret (data.iv)', function (): void {
    $secret = base64_encode(DrawCrypt::getSecret());
    dispatch(new CachingJobWithDrawSecret)->onConnection('database');

    $payload = json_decode((string) DB::table('jobs')->value('payload'), true);
    $payload['data']['iv'] = $payload['data']['secret'];
    unset($payload['data']['secret']);
    DB::table('jobs')->update(['payload' => json_encode($payload)]);

    DrawCrypt::setSecret(DrawCrypt::generateSecret());
    Artisan::call('queue:work', ['connection' => 'database', '--once' => true]);

    expect(Cache::get('job-secret'))->toBe($secret);
});

test('only a job that asks for it carries the draw secret', function (): void {
    dispatch(function (): void {})->onConnection('database');

    $payload = json_decode((string) DB::table('jobs')->value('payload'), true);
    expect($payload['data'])->not->toHaveKey('secret')
        ->and($payload['data'])->toHaveKey('locale');
});

test('a queued notification that asks for it carries the draw secret', function (): void {
    $draw = Draw::factory()->hasParticipants(3)->create();
    config(['queue.default' => 'database']);
    DB::table('jobs')->delete();

    $draw->participants->first()->notify(new TargetDrawn);

    $payload = json_decode((string) DB::table('jobs')->value('payload'), true);
    expect(data_get($payload, 'data.secret'))->toBe(base64_encode(DrawCrypt::getSecret()));
});

test('every queued notification reading encrypted data asks for the draw secret', function (): void {
    foreach (glob(app_path('Notifications/*.php')) ?: [] as $file) {
        $class = 'App\\Notifications\\'.basename($file, '.php');
        if (is_subclass_of($class, ShouldBeEncrypted::class)) {
            expect(is_subclass_of($class, WithDrawSecret::class))->toBeTrue($class);
        }
    }
});
