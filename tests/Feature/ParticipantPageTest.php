<?php

declare(strict_types=1);

use App\Models\Draw;
use App\Notifications\GiftSuggestion;
use App\Notifications\TargetMessages;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

// What the participant page shows besides the messages: their target, the event and the signed gift ideas

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

it('shows who gave a gift idea only when they signed it', function (bool $signed): void {
    Notification::fake();

    $draw = Draw::factory()->hasParticipants(4)->create();
    $sender = $draw->participants->first();
    $recipient = $draw->participants->first(fn ($p): bool => ! $p->is($sender) && ! $p->is($sender->target));
    $santa = $recipient->santa;

    ajaxPost(URL::signedRoute('dearSanta.suggest', ['participant' => $sender]), [
        'recipient' => $recipient->hash,
        'content' => 'A board game',
        'signed' => $signed ? '1' : '0',
    ])->assertSuccessful()->assertJsonPath('suggestion.signed', $signed);

    $expected = $signed ? $sender->name : null;

    // On the santa's page
    $received = ajaxGet(URL::signedRoute('dearSanta.fetch', ['participant' => $santa]))->json('receivedSuggestions');
    expect($received[0]['sender'])->toBe($expected);

    // In the email to the santa, and in their recap
    Notification::assertSentTo($santa, GiftSuggestion::class, function (GiftSuggestion $notification) use ($santa, $expected, $sender, $signed): bool {
        $mail = $notification->toMail($santa);

        return $mail->viewData['senderName'] === $expected
            && ($signed || ! str_contains((string) $mail->render(), $sender->name));
    });
    expect(TargetMessages::viewData($santa)['giftSuggestions'][0]['sender'])->toBe($expected);
})->with(['signed' => true, 'not signed' => false]);

it('keeps the idea, without a name, once its signed sender left the draw', function (): void {
    Notification::fake();

    $draw = Draw::factory()->hasParticipants(4)->create();
    $sender = $draw->participants->first();
    $recipient = $draw->participants->first(fn ($p): bool => ! $p->is($sender) && ! $p->is($sender->target));

    ajaxPost(URL::signedRoute('dearSanta.suggest', ['participant' => $sender]), [
        'recipient' => $recipient->hash, 'content' => 'A scarf', 'signed' => '1',
    ])->assertSuccessful();

    $sender->delete();

    expect(ajaxGet(URL::signedRoute('dearSanta.fetch', ['participant' => $recipient->santa]))->json('receivedSuggestions.0'))
        ->toMatchArray(['mail_body' => 'A scarf', 'sender' => null]);
});
