<?php

declare(strict_types=1);

use App\Events\MailStatusUpdated;
use App\Events\ParticipantUpdated;
use App\Models\Draw;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Broadcast;

test('mail statuses are broadcast on channels the URL hashes do not give away', function (): void {
    Notification::fake();
    $draw = Draw::factory()->hasParticipants(3)->create();
    $participant = $draw->participants->first();

    // Target emails: the organizer panel
    $channel = new MailStatusUpdated($participant->mail)->broadcastOn()[0]->name;
    expect($channel)->toBe($draw->broadcastChannel())->not->toContain($draw->hash);
    ajaxGet(URL::signedRoute('organizerPanel.fetch', ['draw' => $draw]))->assertJsonPath('channel', $channel);

    // Dear Santa messages: only their sender
    ajaxPost(URL::signedRoute('dearSanta.contact', ['participant' => $participant]), ['content' => 'Hello'])->assertSuccessful();
    $dearSanta = $participant->dearSantas()->firstOrFail();
    expect(new MailStatusUpdated($dearSanta->mail)->broadcastOn()[0]->name)
        ->toBe($participant->broadcastChannel())
        ->not->toBe($participant->santa->broadcastChannel());

    ajaxGet(URL::signedRoute('dearSanta.fetch', ['participant' => $participant]))
        ->assertJsonPath('channel', $participant->broadcastChannel())
        ->assertJsonMissingPath('draw');
});

test('a gift idea pushes a content-free update to the sender and the santa, who refetch it', function (): void {
    Notification::fake();
    Event::fake([ParticipantUpdated::class]);

    $draw = Draw::factory()->hasParticipants(4)->create();
    $sender = $draw->participants->first();
    $recipient = $draw->participants->first(fn ($p): bool => ! $p->is($sender) && ! $p->is($sender->target));

    ajaxPost(URL::signedRoute('dearSanta.suggest', ['participant' => $sender]), ['recipient' => $recipient->hash, 'content' => 'A scarf'])
        ->assertSuccessful();

    $channels = [];
    Event::assertDispatched(ParticipantUpdated::class, function (ParticipantUpdated $event) use (&$channels): bool {
        $channels[] = $event->broadcastOn()[0]->name;

        return $event->broadcastWith() === [];
    });
    expect($channels)->toEqualCanonicalizing([$sender->broadcastChannel(), $recipient->santa->broadcastChannel()]);

    expect(ajaxGet(URL::signedRoute('dearSanta.fetchState', ['participant' => $recipient->santa]))->json('receivedSuggestions.0.mail_body'))->toBe('A scarf')
        ->and(ajaxGet(URL::signedRoute('dearSanta.fetchState', ['participant' => $sender]))->json('suggestions'))->toHaveCount(1);
});

test('channels are private, authorized only through the signed links and only for their own channel', function (): void {
    Notification::fake();
    config([
        'broadcasting.default' => 'reverb',
        'broadcasting.connections.reverb.key' => 'key',
        'broadcasting.connections.reverb.secret' => 'secret',
        'broadcasting.connections.reverb.app_id' => 'app',
    ]);
    Broadcast::purge('reverb');

    $draw = Draw::factory()->hasParticipants(3)->create();
    $participant = $draw->participants->first();
    expect(new MailStatusUpdated($participant->mail)->broadcastOn()[0]->name)->toBe('private-'.$draw->broadcastChannel());

    $sign = fn (string $channel): string => 'key:'.hash_hmac('sha256', '1234.5678:private-'.$channel, 'secret');

    // Whatever channel is asked for, the link only signs its own
    ajaxPost(URL::signedRoute('organizerPanel.broadcastAuth', ['draw' => $draw]), ['socket_id' => '1234.5678', 'channel_name' => 'private-'.$participant->broadcastChannel()])
        ->assertOk()->assertJsonPath('auth', $sign($draw->broadcastChannel()));
    ajaxPost(URL::signedRoute('dearSanta.broadcastAuth', ['participant' => $participant]), ['socket_id' => '1234.5678'])
        ->assertOk()->assertJsonPath('auth', $sign($participant->broadcastChannel()));

    // No signature, no channel; nor through Laravel's default endpoint
    ajaxPost(route('dearSanta.broadcastAuth', ['participant' => $participant]), ['socket_id' => '1234.5678'])->assertForbidden();
    ajaxPost('/broadcasting/auth', ['socket_id' => '1234.5678', 'channel_name' => 'private-'.$participant->broadcastChannel()])->assertForbidden();
});
