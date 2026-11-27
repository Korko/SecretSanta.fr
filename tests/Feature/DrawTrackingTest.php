<?php

declare(strict_types=1);

use App\Enums\TrackingState;
use App\Events\DrawTrackingUpdated;
use App\Jobs\SolveDraw;
use App\Models\Draw;
use App\Models\DrawTracking;
use App\Models\Participant;
use App\Models\ParticipantLimit;
use App\Notifications\TargetDrawn;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;

function trackedForm(array $overrides = []): array
{
    return [
        'participant-organizer' => '1',
        'participants' => generateParticipants(3, false),
        'title' => 'this is a test',
        'content-email' => 'test mail {SANTA} => {TARGET}',
        'data-expiration' => date('Y-m-d', strtotime('+2 days')),
        ...$overrides,
    ];
}

// Three participants, each one excluding the two others: no solution
function unsolvableParticipants(): array
{
    $participants = generateParticipants(3, false);
    $participants[0]['exclusions'] = [1, 2];
    $participants[1]['exclusions'] = [0, 2];
    $participants[2]['exclusions'] = [0, 1];

    return $participants;
}

it('answers at once and solves the draw in a job', function (): void {
    Bus::fake();

    $response = ajaxPost('/', trackedForm())
        ->assertStatus(202)
        ->assertJsonPath('tracking.state', 'pending')
        ->assertJsonStructure(['tracking' => ['id', 'token', 'secret', 'channel', 'authUrl']]);

    // Nothing saved before the job: the form is only in its (encrypted) payload
    expect(Draw::query()->count())->toBe(0)
        ->and(Participant::query()->count())->toBe(0)
        ->and(DrawTracking::query()->sole()->uuid)->toBe($response->json('tracking.id'))
        ->and(DrawTracking::query()->sole()->token_hash)->not->toBe($response->json('tracking.token'));

    Bus::assertDispatched(SolveDraw::class);
});

it('keeps the form out of the job payload in clear', function (): void {
    config(['queue.default' => 'database']);

    $participants = generateParticipants(3, false);
    $participants[0]['email'] = 'hidden.santa@example.com';
    ajaxPost('/', trackedForm(['title' => 'Secret title', 'participants' => $participants]))->assertStatus(202);

    $payload = (string) DB::table('jobs')->value('payload');
    expect($payload)->not->toContain('Secret title')->not->toContain('hidden.santa');
});

it('gives the participants in the form order once drawn', function (): void {
    Notification::fake();

    $response = ajaxPost('/', trackedForm())
        ->assertStatus(202)
        ->assertJsonPath('tracking.state', 'drawn');

    $draw = Draw::query()->sole();
    $hashes = $draw->participants->sortBy('id')->pluck('hash')->values()->all();
    expect($response->json('tracking.participants'))->toBe($hashes)
        ->and(DrawTracking::query()->sole()->draw_id)->toBe($draw->id);

    Notification::assertSentTo($draw->participants->first(), TargetDrawn::class);
});

it('keeps nothing but the failed tracking without a solution', function (): void {
    Notification::fake();

    ajaxPost('/', trackedForm(['participants' => unsolvableParticipants()]))
        ->assertStatus(202)
        ->assertJsonPath('tracking.state', 'failed')
        ->assertJsonPath('tracking.reason', 'no_solution');

    expect(Draw::query()->count())->toBe(0)
        ->and(Participant::query()->count())->toBe(0)
        ->and(DB::table('exclusions')->count())->toBe(0)
        ->and(DrawTracking::query()->sole()->state)->toBe(TrackingState::Failed);

    Notification::assertNothingSent();
});

it('does not make a second draw when the job runs again', function (): void {
    Notification::fake();
    Bus::fake();

    ajaxPost('/', trackedForm())->assertStatus(202);
    $job = Bus::dispatched(SolveDraw::class)->first();

    $job->handle();
    $job->handle();

    expect(Draw::query()->count())->toBe(1);
});

it('fails the tracking once the job is out of tries', function (): void {
    Bus::fake();

    ajaxPost('/', trackedForm())->assertStatus(202);
    Bus::dispatched(SolveDraw::class)->first()->failed(new RuntimeException('Database down'));

    expect(DrawTracking::query()->sole())
        ->state->toBe(TrackingState::Failed)
        ->failure_reason->toBe(DrawTracking::REASON_ERROR);
});

it('fails the tracking when the limit code was used by another draw in the meantime', function (): void {
    Notification::fake();
    Bus::fake();
    [, $code] = ParticipantLimit::generate(6);

    ajaxPost('/', trackedForm(['limit' => $code, 'participants' => generateParticipants(5, false)]))->assertStatus(202);
    ajaxPost('/', trackedForm(['limit' => $code, 'participants' => generateParticipants(5, false)]))->assertStatus(202);

    Bus::dispatched(SolveDraw::class)->each(fn (SolveDraw $job) => $job->handle());

    expect(Draw::query()->count())->toBe(1)
        ->and(DrawTracking::query()->where('state', 'failed')->sole()->failure_reason)->toBe(DrawTracking::REASON_LIMIT);
});

it('only tells whether the draw went through without the token', function (): void {
    Notification::fake();

    $tracking = ajaxPost('/', trackedForm())->json('tracking');

    ajaxGet('/tracking/'.$tracking['id'])->assertExactJson(['state' => 'drawn']);
    ajaxGet('/tracking/'.$tracking['id'], ['X-TRACKING-TOKEN' => 'wrong'])->assertExactJson(['state' => 'drawn']);
    ajaxGet('/tracking/'.$tracking['id'], ['X-TRACKING-TOKEN' => $tracking['token']])
        ->assertJsonPath('state', 'drawn')
        ->assertJsonPath('participants', $tracking['participants']);

    // Not by its id
    ajaxGet('/tracking/'.DrawTracking::query()->sole()->id)->assertNotFound();
});

it('broadcasts the end of the draw after its commit, on its private channel', function (): void {
    Notification::fake();
    Event::fake([DrawTrackingUpdated::class]);

    ajaxPost('/', trackedForm())->assertStatus(202);

    $tracking = DrawTracking::query()->sole();
    Event::assertDispatched(DrawTrackingUpdated::class, fn (DrawTrackingUpdated $event): bool => $event->broadcastOn()[0]->name === 'private-'.$tracking->broadcastChannel()
        && $event->broadcastWith()['state'] === 'drawn'
        && ! str_contains($event->broadcastOn()[0]->name, $tracking->uuid));
});

it('authorizes the private channel with the token only', function (): void {
    Notification::fake();
    Bus::fake();
    config(['broadcasting.default' => 'reverb', 'broadcasting.connections.reverb.key' => 'key', 'broadcasting.connections.reverb.secret' => 'secret', 'broadcasting.connections.reverb.app_id' => 'app']);

    $tracking = ajaxPost('/', trackedForm())->json('tracking');

    ajaxPost($tracking['authUrl'], ['socket_id' => '1.2'])->assertForbidden();
    ajaxPost($tracking['authUrl'], ['socket_id' => '1.2'], ['X-TRACKING-TOKEN' => 'wrong'])->assertForbidden();
    ajaxPost($tracking['authUrl'], ['socket_id' => '1.2'], ['X-TRACKING-TOKEN' => $tracking['token']])
        ->assertSuccessful()
        ->assertJsonStructure(['auth']);
});

it('prunes the trackings left without a draw', function (): void {
    [$old] = DrawTracking::start();
    $old->created_at = now()->subDays(2);
    $old->save();
    [$recent] = DrawTracking::start();

    $this->artisan('model:prune', ['--model' => DrawTracking::class])->assertSuccessful();

    expect(DrawTracking::query()->pluck('id')->all())->toBe([$recent->id]);
});
