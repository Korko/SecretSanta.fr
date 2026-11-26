<?php

declare(strict_types=1);

use App\Jobs\ComputeNextSolvable;
use App\Models\Draw;
use App\Models\Participant;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\URL;

test('the draw computes the final CSV availability in a job, not in the request', function (): void {
    Queue::fake();

    $draw = Draw::factory()->hasParticipants(4)->create();

    expect($draw->fresh()->next_solvable)->toBeNull();
    Queue::assertPushed(ComputeNextSolvable::class, fn (ComputeNextSolvable $job): bool => $job->draw->is($draw));
});

test('a change of participants computes it again in a job', function (): void {
    $draw = Draw::factory()->hasParticipants(4)->create();
    Notification::fake();
    Queue::fake();

    ajaxPost(URL::signedRoute('organizerPanel.changeParticipants', ['draw' => $draw]), [
        'add' => [['name' => 'N1', 'email' => 'n1@example.com']],
    ])->assertSuccessful()->assertJsonPath('finalCsvAvailable', null);

    expect($draw->fresh()->next_solvable)->toBeNull();
    Queue::assertPushed(ComputeNextSolvable::class);
});

test('the job carries no draw secret and reads nothing encrypted', function (): void {
    $draw = Draw::factory()->hasParticipants(4)->create();
    Draw::query()->whereKey($draw->id)->update(['next_solvable' => null]);

    dispatch(new ComputeNextSolvable($draw))->onConnection('database');

    $payload = json_decode((string) DB::table('jobs')->value('payload'), true);
    expect($payload['data'])->not->toHaveKey('secret');

    // Another secret: anything encrypted would fail to decrypt
    DrawCrypt::setSecret(DrawCrypt::generateSecret());
    Artisan::call('queue:work', ['connection' => 'database', '--once' => true]);

    expect(DB::table('jobs')->count())->toBe(0)
        ->and($draw->fresh()->next_solvable)->toBeTrue();
});

test('the job finds when the same participants cannot be drawn again', function (): void {
    // 3 participants: A -> B -> C -> A, the only other cycle is A -> C -> B -> A. With 2 of them in each other's
    // exclusions, there is a single possible cycle, which this year's targets then exclude
    $draw = Draw::factory()->hasParticipants(3)->create();
    $participants = $draw->participants()->with('target')->get();
    $first = $participants->first();
    $other = $participants->first(fn (Participant $participant): bool => ! $participant->is($first->target) && ! $participant->is($first));
    $first->exclusions()->attach($other);

    expect(ComputeNextSolvable::compute($draw))->toBeFalse()
        ->and($draw->fresh()->next_solvable)->toBeFalse();
});

test('the final CSV is not computed in the request when not known yet', function (): void {
    $draw = Draw::factory()->hasParticipants(3)->expired()->create();
    Draw::query()->whereKey($draw->id)->update(['next_solvable' => null]);

    ajaxGet(URL::signedRoute('organizerPanel.csvFinal', ['draw' => $draw]))->assertNotFound();

    expect($draw->fresh()->next_solvable)->toBeNull();

    ComputeNextSolvable::compute($draw);

    ajaxGet(URL::signedRoute('organizerPanel.csvFinal', ['draw' => $draw]))->assertSuccessful();
});

test('the panel state tells the final CSV availability once known', function (): void {
    $draw = Draw::factory()->hasParticipants(3)->create();
    Draw::query()->whereKey($draw->id)->update(['next_solvable' => null]);

    ajaxGet(URL::signedRoute('organizerPanel.fetchState', ['draw' => $draw]))->assertJsonPath('finalCsvAvailable', null);

    ComputeNextSolvable::compute($draw);

    ajaxGet(URL::signedRoute('organizerPanel.fetchState', ['draw' => $draw]))->assertJsonPath('finalCsvAvailable', true);
});
