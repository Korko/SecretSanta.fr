<?php

declare(strict_types=1);

use App\Models\Draw;
use App\Notifications\GiftSuggestion;
use App\Notifications\TargetMessages;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

// What the participant page shows besides the messages: their target, the event and the gift ideas

it('gives the participant page their target and the event', function (): void {
    $draw = Draw::factory()->hasParticipants(3)->create([
        'mail_title' => 'Noël chez Martin, {SANTA}',
        'expires_at' => now()->addDays(10)->startOfDay(),
        'location' => 'Chez Martin',
        'budget' => '15',
    ]);
    $participant = $draw->participants->first();

    expect(ajaxGet(URL::signedRoute('dearSanta.fetch', ['participant' => $participant]))->assertSuccessful()->json())
        ->toMatchArray([
            'target' => $participant->target->name,
            'title' => 'Noël chez Martin, '.$participant->name,
        ])
        ->and(ajaxGet(URL::signedRoute('dearSanta.fetch', ['participant' => $participant]))->json('event'))
        ->toMatchArray(['expires_at' => now()->addDays(10)->toDateString(), 'location' => 'Chez Martin'])
        ->toHaveKey('budget');
});

it('leaves the location and the budget out when not given', function (): void {
    $draw = Draw::factory()->hasParticipants(3)->create(['location' => null, 'budget' => null]);

    expect(ajaxGet(URL::signedRoute('dearSanta.fetch', ['participant' => $draw->participants->first()]))->json('event'))
        ->toMatchArray(['location' => null, 'budget' => null]);
});

it('gives the organizer panel the event name without the participant keywords', function (): void {
    $draw = Draw::factory()->hasParticipants(3)->create(['mail_title' => 'Noël chez Martin, {SANTA}']);

    expect(ajaxGet(URL::signedRoute('organizerPanel.fetch', ['draw' => $draw]))->json('title'))->toBe('Noël chez Martin');
});

it('never tells the santa who gave a gift idea', function (): void {
    Notification::fake();

    $draw = Draw::factory()->hasParticipants(4)->create();
    $sender = $draw->participants->first();
    $recipient = $draw->participants->first(fn ($p): bool => ! $p->is($sender) && ! $p->is($sender->target));
    $santa = $recipient->santa;

    ajaxPost(URL::signedRoute('dearSanta.suggest', ['participant' => $sender]), [
        'recipient' => $recipient->hash,
        'content' => 'A board game',
    ])->assertSuccessful();

    expect(ajaxGet(URL::signedRoute('dearSanta.fetch', ['participant' => $santa]))->json('receivedSuggestions.0'))
        ->not->toHaveKey('sender')
        ->and(TargetMessages::viewData($santa)['giftSuggestions'][0])->not->toHaveKey('sender');
    Notification::assertSentTo($santa, GiftSuggestion::class, fn (GiftSuggestion $notification): bool => ! str_contains((string) $notification->toMail($santa)->render(), $sender->name));
});
