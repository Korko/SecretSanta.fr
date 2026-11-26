<?php

declare(strict_types=1);

use App\Enums\DeliveryStatus;
use App\Events\MailStatusUpdated;
use App\Models\Draw;
use App\Models\Participant;
use App\Notifications\DearSanta;
use App\Notifications\GiftSuggestion;
use App\Notifications\ParticipantEmailCode;
use App\Notifications\ParticipantWithdrew;
use App\Notifications\TargetChanged;
use App\Notifications\TargetDrawn;
use App\Notifications\TargetMessages;
use App\Notifications\TargetWithdrawn;
use App\Services\URLParser;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

use function PHPUnit\Framework\assertEquals;

it('send to each participant a link to write to their santa', function (): void {
    Notification::fake();

    $draw = Draw::factory()
        ->hasParticipants(3)
        ->create();

    foreach ($draw->participants as $participant) {
        Notification::assertSentTo($participant, function (TargetDrawn $notification) use ($participant): true {
            $link = $notification->toMail($participant)->data()['dearSantaLink'];

            // Check the dearSanta link is valid
            test()->get($link)->assertSuccessful();

            // Check link can be used for support
            $path = parse_url($link, PHP_URL_PATH);
            $guessedParticipant = resolve(URLParser::class)->participant((string) $path);
            assertEquals($participant->id, $guessedParticipant->id);

            return true;
        });
    }
});

it('lets each participant write to their santa', function (): void {
    Notification::fake();

    $draw = Draw::factory()
        ->hasParticipants(3)
        ->create();

    foreach ($draw->participants as $participant) {
        ajaxPost(URL::signedRoute('dearSanta.contact', ['participant' => $participant]), [
            'content' => 'test dearSanta mail content',
        ])
            ->assertSuccessful()
            ->assertJsonStructure(['message']);

        Notification::assertSentTo($participant->santa, DearSanta::class);
    }
});

it('refuses a message to the santa longer than 2000 characters', function (): void {
    Notification::fake();

    $participant = Draw::factory()->hasParticipants(3)->create()->participants->first();

    ajaxPost(URL::signedRoute('dearSanta.contact', ['participant' => $participant]), [
        'content' => str_repeat('a', 2001),
    ])->assertStatus(422)->assertJsonValidationErrors('content');

    // The draw itself sent the target emails: only the message must not have gone
    Notification::assertNotSentTo($participant->santa, DearSanta::class);
});

it('only tells a message sender whether it was sent, never whether it was read or bounced', function (DeliveryStatus $status): void {
    Notification::fake();

    $draw = Draw::factory()->hasParticipants(3)->create();
    $participant = $draw->participants->first();

    ajaxPost(URL::signedRoute('dearSanta.contact', ['participant' => $participant]), [
        'content' => 'test dearSanta mail content',
    ])->assertSuccessful();

    $mail = $participant->dearSantas()->firstOrFail()->mail;
    $mail->updateDeliveryStatus($status);

    expect(new MailStatusUpdated($mail->fresh())->broadcastWhen())->toBeFalse();

    $emails = ajaxGet(URL::signedRoute('dearSanta.fetch', ['participant' => $participant]))->json('emails');
    expect(array_values($emails)[0]['mail'])->toMatchArray(['delivery_status' => 'sent'])
        ->not->toHaveKey('error_code');
})->with([DeliveryStatus::Received, DeliveryStatus::Error, DeliveryStatus::Delayed]);

it('leaves resending a message to the organizer', function (): void {
    expect(Route::has('dearSanta.resend'))->toBeFalse();
});

it('does not resend anything on a GET, like a link prefetch or an email scanner', function (): void {
    $draw = Draw::factory()->hasParticipants(3)->create();
    $participant = $draw->participants->first();

    ajaxGet(URL::signedRoute('dearSanta.resend_target', ['participant' => $participant]))
        ->assertStatus(405);
});

// The code mailed to the new address, as the participant would read it
function requestEmailChange(Participant $participant, string $email): string
{
    ajaxPost(URL::signedRoute('dearSanta.request_email_change', ['participant' => $participant]), ['email' => $email])
        ->assertSuccessful()
        ->assertJson(['pendingEmail' => $email]);

    $code = null;
    Notification::assertSentOnDemand(ParticipantEmailCode::class, function (ParticipantEmailCode $notification, array $channels, object $notifiable) use ($email, &$code): bool {
        if (array_key_first($notifiable->routes['mail']) !== $email) {
            return false;
        }

        $code = $notification->toMail($notifiable)->viewData['code'];

        return true;
    });

    return (string) $code;
}

it('lets a participant move to another address once they typed the code sent there', function (): void {
    Notification::fake();

    $draw = Draw::factory()->hasParticipants(3)->create();
    $participant = $draw->participants->first();
    $previous = $participant->email;

    $code = requestEmailChange($participant, 'new@example.com');
    expect($participant->fresh()->email)->toBe($previous);

    $confirm = URL::signedRoute('dearSanta.change_email', ['participant' => $participant]);
    ajaxPost($confirm, ['code' => $code === '000000' ? '111111' : '000000'])->assertStatus(422);
    expect($participant->fresh()->email)->toBe($previous);

    ajaxPost($confirm, ['code' => $code])->assertSuccessful()->assertJson(['email' => 'new@example.com']);

    $participant = $participant->fresh();
    expect($participant->email)->toBe('new@example.com')
        ->and($participant->pending_email)->toBeNull();
    Notification::assertSentToTimes($participant, TargetDrawn::class, 2);

    // Used once
    ajaxPost($confirm, ['code' => $code])->assertStatus(422);
});

it('drops an address change after too many wrong codes or an hour', function (): void {
    Notification::fake();

    $draw = Draw::factory()->hasParticipants(3)->create();
    $participant = $draw->participants->first();
    $confirm = URL::signedRoute('dearSanta.change_email', ['participant' => $participant]);

    $code = requestEmailChange($participant, 'new@example.com');
    $wrong = $code === '000000' ? '111111' : '000000';
    foreach (range(1, 5) as $ignored) {
        ajaxPost($confirm, ['code' => $wrong])->assertStatus(422);
    }

    ajaxPost($confirm, ['code' => $code])->assertStatus(422);

    $this->travel(2)->minutes();
    $code = requestEmailChange($participant, 'other@example.com');
    $this->travel(61)->minutes();

    ajaxPost($confirm, ['code' => $code])->assertStatus(422);
    expect($participant->fresh()->email)->not->toBe('other@example.com');
});

it('refuses to move a participant to their own or a refused address', function (): void {
    Notification::fake();

    $draw = Draw::factory()->hasParticipants(3)->create();
    $participant = $draw->participants->first();
    $draw->refuseEmail('refused@example.com');
    $request = URL::signedRoute('dearSanta.request_email_change', ['participant' => $participant]);

    ajaxPost($request, ['email' => mb_strtoupper($participant->email)])->assertStatus(422);
    ajaxPost($request, ['email' => 'refused@example.com'])->assertStatus(422);
    ajaxPost($request, ['email' => 'not an email'])->assertStatus(422);

    Notification::assertSentOnDemandTimes(ParticipantEmailCode::class, 0);
});

it('lets a participant withdraw: their santa gets a new target and the organizer is told', function (): void {
    Notification::fake();

    $draw = Draw::factory()->hasParticipants(4)->create();
    $participant = $draw->participants->first();
    $santa = $participant->santa;

    ajaxPost(URL::signedRoute('dearSanta.withdraw', ['participant' => $participant]))
        ->assertSuccessful()
        ->assertJsonStructure(['message']);

    // Kept aside, for the organizer to bring them back: out of the draw, their page gone
    expect($participant->fresh()->withdrawn_at)->not->toBeNull()
        ->and($participant->fresh()->santa_id)->toBeNull()
        ->and($draw->participants()->count())->toBe(3)
        ->and($draw->withdrawnParticipants()->count())->toBe(1)
        ->and($santa->fresh()->target->is($participant))->toBeFalse();
    Notification::assertSentTo($santa, TargetWithdrawn::class);
    Notification::assertSentTo($draw, ParticipantWithdrew::class, function (ParticipantWithdrew $notification) use ($draw): bool {
        $mail = $notification->toMail($draw);

        // How to bring them back
        return str_contains((string) $mail->subject, '#'.$draw->id) && str_contains((string) $mail->render(), '/org/'.$draw->hash);
    });
    ajaxGet(URL::signedRoute('dearSanta.fetch', ['participant' => $participant]))->assertNotFound();
});

it('keeps at least 3 participants when one withdraws', function (): void {
    Notification::fake();

    $draw = Draw::factory()->hasParticipants(3)->create();
    $participant = $draw->participants->first();

    ajaxPost(URL::signedRoute('dearSanta.withdraw', ['participant' => $participant]))->assertStatus(422);

    expect($draw->participants()->count())->toBe(3);
    Notification::assertNotSentTo($draw, ParticipantWithdrew::class);
});

it('does not let the organizer withdraw from their participant page', function (): void {
    $draw = Draw::factory()->hasParticipants(4)->create();
    $participant = $draw->participants->first();
    $draw->organizer_participant_id = $participant->id;
    $draw->save();

    ajaxPost(URL::signedRoute('dearSanta.withdraw', ['participant' => $participant]))->assertForbidden();
    expect($participant->fresh())->not->toBeNull();
});

it('does not give a santa a target they excluded when theirs withdraws', function (): void {
    Notification::fake();

    $draw = Draw::factory()->hasParticipants(5)->create();
    // A -> B -> C, A and C exclude each other
    $a = $draw->participants->first(fn ($p): bool => $p->target->target->isNot($p));
    $b = $a->target;
    $c = $b->target;
    $a->exclusions()->attach($c);
    $c->exclusions()->attach($a);

    ajaxPost(URL::signedRoute('dearSanta.withdraw', ['participant' => $b]))->assertSuccessful();

    $draw->refresh();
    expect($a->fresh()->target->is($c))->toBeFalse()
        ->and($draw->participants)->toHaveCount(4);
    // Still everyone gives to someone else, and is given to once
    $targets = $draw->participants->map(fn ($p) => $p->target->id);
    expect($targets->unique())->toHaveCount(4)
        ->and($draw->participants->every(fn ($p): bool => $p->target->isNot($p)))->toBeTrue();
});

it('sends again, in a single email, every message the target wrote to their santa and the gift ideas given for them', function (): void {
    Notification::fake();

    $draw = Draw::factory()->hasParticipants(4)->create();
    $participant = $draw->participants->first();
    $target = $participant->target;

    foreach (['first message', 'second message'] as $content) {
        ajaxPost(URL::signedRoute('dearSanta.contact', ['participant' => $target]), ['content' => $content])->assertSuccessful();
    }

    $sender = $draw->participants->firstOrFail(fn ($p): bool => ! $p->is($participant) && ! $p->is($target));
    foreach (['first idea', 'second idea'] as $content) {
        ajaxPost(URL::signedRoute('dearSanta.suggest', ['participant' => $sender]), ['recipient' => $target->hash, 'content' => $content])->assertSuccessful();
    }

    // What they wrote to their own santa is not part of it
    ajaxPost(URL::signedRoute('dearSanta.contact', ['participant' => $participant]), ['content' => 'my own message'])->assertSuccessful();

    ajaxPost(URL::signedRoute('dearSanta.resend_target', ['participant' => $participant]))
        ->assertSuccessful()
        ->assertJsonStructure(['message']);

    Notification::assertSentToTimes($participant, TargetMessages::class, 1);
    Notification::assertSentTo($participant, TargetMessages::class, function (TargetMessages $notification) use ($participant): bool {
        $html = (string) $notification->toMail($participant)->render();

        return str_contains($html, 'first message') && str_contains($html, 'second message') && ! str_contains($html, 'my own message')
            && str_contains($html, 'first idea') && str_contains($html, 'second idea');
    });

    // The messages and ideas themselves are not sent again: their status, on their senders' pages, stays as it was
    Notification::assertSentToTimes($participant, DearSanta::class, 2);
    Notification::assertSentToTimes($participant, GiftSuggestion::class, 2);

    // Once per resend delay
    ajaxPost(URL::signedRoute('dearSanta.resend_target', ['participant' => $participant]))->assertForbidden();
    Notification::assertSentToTimes($participant, TargetMessages::class, 1);
});

it("sends the target's messages to the participant's new address too, in their target email", function (): void {
    Notification::fake();

    $draw = Draw::factory()->hasParticipants(3)->create();
    $participant = $draw->participants->first();
    ajaxPost(URL::signedRoute('dearSanta.contact', ['participant' => $participant->target]), ['content' => 'lost message'])
        ->assertSuccessful();

    $code = requestEmailChange($participant, 'new@example.com');
    Notification::fake(); // only what the change of address sends
    ajaxPost(URL::signedRoute('dearSanta.change_email', ['participant' => $participant]), ['code' => $code])->assertSuccessful();

    // A single email: their target and what they wrote
    Notification::assertSentToTimes($participant, TargetDrawn::class, 1);
    Notification::assertSentTo($participant, TargetDrawn::class, fn (TargetDrawn $notification): bool => str_contains((string) $notification->toMail($participant)->render(), 'lost message'));
    Notification::assertNotSentTo($participant, TargetMessages::class);
});

it('confirms a reception once: reloading the page neither writes nor broadcasts it again', function (): void {
    Notification::fake();

    $draw = Draw::factory()->hasParticipants(3)->create();
    $participant = $draw->participants->first();
    Event::fake([MailStatusUpdated::class]);

    ajaxGet(URL::signedRoute('dearSanta.fetch', ['participant' => $participant]))->assertSuccessful();
    ajaxGet(URL::signedRoute('dearSanta.fetch', ['participant' => $participant]))->assertSuccessful();

    Event::assertDispatchedTimes(MailStatusUpdated::class, 1);
});

it('lets a participant suggest a gift idea to the santa of someone else than their target', function (): void {
    Notification::fake();

    $draw = Draw::factory()->hasParticipants(4)->create();
    $sender = $draw->participants->first();
    $recipient = $draw->participants->first(fn ($p): bool => ! $p->is($sender) && ! $p->is($sender->target));

    $fetch = ajaxGet(URL::signedRoute('dearSanta.fetch', ['participant' => $sender]))->assertSuccessful();
    expect(array_keys($fetch->json('suggestionRecipients')))
        ->toHaveCount(2)
        ->not->toContain($sender->hash)
        ->not->toContain($sender->target->hash);

    ajaxPost(URL::signedRoute('dearSanta.suggest', ['participant' => $sender]), [
        'recipient' => $recipient->hash,
        'content' => 'A board game',
    ])->assertSuccessful()->assertJsonPath('suggestion.recipient', $recipient->name);

    Notification::assertSentTo($recipient->santa, GiftSuggestion::class, function (GiftSuggestion $notification) use ($recipient, $sender): bool {
        $data = $notification->toMail($recipient->santa)->viewData;

        return $data['content'] === 'A board game' && $data['targetName'] === $recipient->name
            && ! str_contains((string) $notification->toMail($recipient->santa)->render(), $sender->name);
    });

    expect(ajaxGet(URL::signedRoute('dearSanta.fetch', ['participant' => $sender]))->json('suggestions'))->toHaveCount(1);

    // Also on the santa's page, as a recap
    $received = ajaxGet(URL::signedRoute('dearSanta.fetch', ['participant' => $recipient->santa]))->json('receivedSuggestions');
    expect($received)->toHaveCount(1)
        ->and($received[0])->toMatchArray(['mail_body' => 'A board game'])
        // Not signed: nothing tells who gave it
        ->and($received[0]['sender'])->toBeNull();
});

it("refuses a gift idea for oneself or one's own target", function (): void {
    Notification::fake();

    $draw = Draw::factory()->hasParticipants(4)->create();
    $sender = $draw->participants->first();
    $recipient = $draw->participants->first(fn ($p): bool => ! $p->is($sender) && ! $p->is($sender->target));
    $suggest = URL::signedRoute('dearSanta.suggest', ['participant' => $sender]);

    ajaxPost($suggest, ['recipient' => $sender->target->hash, 'content' => 'Socks'])->assertStatus(422);
    ajaxPost($suggest, ['recipient' => $sender->hash, 'content' => 'Socks'])->assertStatus(422);
    ajaxPost($suggest, ['recipient' => $recipient->hash, 'content' => ''])->assertStatus(422);

    ajaxPost($suggest, ['recipient' => $recipient->hash, 'content' => 'Socks'])->assertSuccessful();
});

it('forwards gift ideas to the new santa when the recipient changes santa', function (): void {
    Notification::fake();

    $draw = Draw::factory()->hasParticipants(5)->create();
    $sender = $draw->participants->first();
    $recipient = $draw->participants->first(fn ($p): bool => ! $p->is($sender) && ! $p->is($sender->target) && ! $p->santa->is($sender->target));
    ajaxPost(URL::signedRoute('dearSanta.suggest', ['participant' => $sender]), ['recipient' => $recipient->hash, 'content' => 'A book'])
        ->assertSuccessful();

    // Their santa leaves: the santa's own santa now gives to the recipient, and gets the idea
    ajaxPost(URL::signedRoute('dearSanta.withdraw', ['participant' => $recipient->santa]))->assertSuccessful();

    // In the email telling them about their new target, unless it is their own idea
    $newSanta = $recipient->fresh()->santa;
    $emails = Notification::sent($newSanta, TargetWithdrawn::class)->merge(Notification::sent($newSanta, TargetChanged::class));
    expect($emails)->toHaveCount(1)
        ->and(str_contains((string) $emails->first()->toMail($newSanta)->render(), 'A book'))->toBe(! $newSanta->is($sender));
    Notification::assertNotSentTo($newSanta, GiftSuggestion::class);
});

it('sends a participant added to the draw the gift ideas already given for their target, in their target email', function (): void {
    Notification::fake();

    $draw = Draw::factory()->hasParticipants(4)->create();
    foreach ($draw->participants as $recipient) {
        $sender = $draw->participants->firstOrFail(fn ($p): bool => ! $p->is($recipient) && ! $p->is($recipient->santa));
        ajaxPost(URL::signedRoute('dearSanta.suggest', ['participant' => $sender]), ['recipient' => $recipient->hash, 'content' => 'idea for '.$recipient->name])
            ->assertSuccessful();
    }

    ajaxPost(URL::signedRoute('organizerPanel.changeParticipants', ['draw' => $draw]), [
        'add' => [['name' => 'Newcomer', 'email' => 'newcomer@example.com']],
    ])->assertSuccessful();

    $newcomer = $draw->participants()->get()->firstOrFail(fn ($participant): bool => $participant->name === 'Newcomer');
    Notification::assertSentToTimes($newcomer, TargetDrawn::class, 1);
    Notification::assertSentTo($newcomer, TargetDrawn::class, fn (TargetDrawn $notification): bool => str_contains((string) $notification->toMail($newcomer)->render(), e('idea for '.$newcomer->target->name)));
    Notification::assertNotSentTo($newcomer, GiftSuggestion::class);
});

it("sends the gift ideas given for their target to the participant's new address too, in their target email", function (): void {
    Notification::fake();

    $draw = Draw::factory()->hasParticipants(4)->create();
    $participant = $draw->participants->first();
    $sender = $draw->participants->firstOrFail(fn ($p): bool => ! $p->is($participant) && ! $p->is($participant->target));
    ajaxPost(URL::signedRoute('dearSanta.suggest', ['participant' => $sender]), ['recipient' => $participant->target->hash, 'content' => 'A scarf'])
        ->assertSuccessful();

    $code = requestEmailChange($participant, 'new@example.com');
    Notification::fake(); // only what the change of address sends
    ajaxPost(URL::signedRoute('dearSanta.change_email', ['participant' => $participant]), ['code' => $code])->assertSuccessful();

    // Not on its own again: within the target email, at the new address
    Notification::assertNotSentTo($participant, GiftSuggestion::class);
    Notification::assertSentToTimes($participant, TargetDrawn::class, 1);
    Notification::assertSentTo($participant, TargetDrawn::class, fn (TargetDrawn $notification): bool => str_contains((string) $notification->toMail($participant)->render(), 'A scarf'));
});

it('only tells a gift idea sender whether it was sent, not when it was read', function (): void {
    Notification::fake();

    $draw = Draw::factory()->hasParticipants(4)->create();
    $sender = $draw->participants->first();
    $recipient = $draw->participants->first(fn ($p): bool => ! $p->is($sender) && ! $p->is($sender->target));
    $suggest = URL::signedRoute('dearSanta.suggest', ['participant' => $sender]);

    ajaxPost($suggest, ['recipient' => $recipient->hash, 'content' => str_repeat('a', 2001)])->assertStatus(422);
    ajaxPost($suggest, ['recipient' => $recipient->hash, 'content' => 'A mug'])->assertSuccessful();

    $mail = $sender->sentSuggestions()->firstOrFail()->mail;
    $mail->markAsReceived();

    expect(new MailStatusUpdated($mail->fresh())->broadcastWhen())->toBeFalse();

    $suggestions = ajaxGet(URL::signedRoute('dearSanta.fetch', ['participant' => $sender]))->json('suggestions');
    expect(array_values($suggestions)[0]['mail']['delivery_status'])->toBe('sent');
});

it('keeps the gift ideas of a participant who withdraws', function (): void {
    Notification::fake();

    $draw = Draw::factory()->hasParticipants(5)->create();
    $sender = $draw->participants->first();
    $recipient = $draw->participants->first(fn ($p): bool => ! $p->is($sender) && ! $p->is($sender->target) && ! $p->santa->is($sender));
    ajaxPost(URL::signedRoute('dearSanta.suggest', ['participant' => $sender]), ['recipient' => $recipient->hash, 'content' => 'A plant'])
        ->assertSuccessful();

    ajaxPost(URL::signedRoute('dearSanta.withdraw', ['participant' => $sender]))->assertSuccessful();

    $received = ajaxGet(URL::signedRoute('dearSanta.fetch', ['participant' => $recipient->fresh()->santa]))->json('receivedSuggestions');
    expect($received)->toHaveCount(1)
        ->and($received[0])->toMatchArray(['mail_body' => 'A plant']);
});

it('shows the organizer who wrote to their santa, never to whom', function (): void {
    Notification::fake();

    $draw = Draw::factory()
        ->hasParticipants(3)
        ->create();

    [$writer, $silent] = [$draw->participants[0], $draw->participants[1]];

    ajaxPost(URL::signedRoute('dearSanta.contact', ['participant' => $writer]), [
        'content' => 'test dearSanta mail content',
    ])->assertSuccessful();

    $panel = ajaxGet(URL::signedRoute('organizerPanel.fetch', ['draw' => $draw]))
        ->assertSuccessful()
        ->assertJsonPath("participants.{$writer->hash}.wrote_to_santa", true)
        ->assertJsonPath("participants.{$silent->hash}.wrote_to_santa", false);

    // Nothing about the santa it went to
    expect($panel->json("participants.{$writer->hash}"))->not->toHaveKeys(['santa', 'target', 'dear_santas']);
});
