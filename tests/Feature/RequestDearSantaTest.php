<?php

declare(strict_types=1);

use App\Models\Draw;
use App\Models\Participant;
use App\Notifications\DearSanta;
use App\Notifications\GiftSuggestion;
use App\Notifications\ParticipantEmailCode;
use App\Notifications\ParticipantWithdrew;
use App\Notifications\TargetDrawn;
use App\Notifications\TargetWithdrawn;
use App\Services\URLParser;
use Illuminate\Support\Facades\Notification;
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

it('lets a participant resend the email to their santa in case of error', function (): void {
    Notification::fake();

    $draw = Draw::factory()
        ->hasParticipants(3)
        ->create();

    $participant = $draw->participants->first();

    ajaxPost(URL::signedRoute('dearSanta.contact', ['participant' => $participant]), [
        'content' => 'test dearSanta mail content',
    ])
        ->assertSuccessful()
        ->assertJsonStructure(['message']);

    Notification::assertSentToTimes($participant->santa, DearSanta::class, 1);
    $dearSanta = $participant->dearSantas->first();

    ajaxPost(URL::signedRoute('dearSanta.resend', ['participant' => $participant, 'dearSanta' => $dearSanta]))
        ->assertForbidden()
        ->assertJsonStructure(['message']);

    // Nothing new, still the same notification
    Notification::assertSentToTimes($participant->santa, DearSanta::class, 1);

    $dearSanta->mail->updated_at = $dearSanta->mail->updated_at->subSeconds(config('mail.resend_delay'));
    $dearSanta->mail->save();

    ajaxPost(URL::signedRoute('dearSanta.resend', ['participant' => $participant, 'dearSanta' => $dearSanta]))
        ->assertSuccessful()
        ->assertJsonStructure(['message']);

    Notification::assertSentToTimes($participant->santa, DearSanta::class, 2);
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

    expect($participant->fresh())->toBeNull()
        ->and($draw->participants()->count())->toBe(3)
        ->and($santa->fresh()->target->is($participant))->toBeFalse();
    Notification::assertSentTo($santa, TargetWithdrawn::class);
    Notification::assertSentTo($draw, ParticipantWithdrew::class, fn (ParticipantWithdrew $notification): bool => str_contains((string) $notification->toMail($draw)->subject, '#'.$draw->id));
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
        'signed' => 1,
    ])->assertSuccessful()->assertJsonPath('suggestion.recipient', $recipient->name);

    Notification::assertSentTo($recipient->santa, GiftSuggestion::class, function (GiftSuggestion $notification) use ($recipient, $sender): bool {
        $data = $notification->toMail($recipient->santa)->viewData;

        return $data['content'] === 'A board game' && $data['targetName'] === $recipient->name && $data['senderName'] === $sender->name;
    });

    expect(ajaxGet(URL::signedRoute('dearSanta.fetch', ['participant' => $sender]))->json('suggestions'))->toHaveCount(1);
});

it("keeps a gift idea anonymous unless signed, and refuses one for oneself or one's own target", function (): void {
    Notification::fake();

    $draw = Draw::factory()->hasParticipants(4)->create();
    $sender = $draw->participants->first();
    $recipient = $draw->participants->first(fn ($p): bool => ! $p->is($sender) && ! $p->is($sender->target));
    $suggest = URL::signedRoute('dearSanta.suggest', ['participant' => $sender]);

    ajaxPost($suggest, ['recipient' => $sender->target->hash, 'content' => 'Socks'])->assertStatus(422);
    ajaxPost($suggest, ['recipient' => $sender->hash, 'content' => 'Socks'])->assertStatus(422);
    ajaxPost($suggest, ['recipient' => $recipient->hash, 'content' => ''])->assertStatus(422);

    ajaxPost($suggest, ['recipient' => $recipient->hash, 'content' => 'Socks'])->assertSuccessful();
    Notification::assertSentTo($recipient->santa, GiftSuggestion::class, fn (GiftSuggestion $notification): bool => $notification->toMail($recipient->santa)->viewData['senderName'] === null);
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

    $newSanta = $recipient->fresh()->santa;
    if ($newSanta->is($sender)) {
        Notification::assertNotSentTo($newSanta, GiftSuggestion::class);
    } else {
        Notification::assertSentTo($newSanta, GiftSuggestion::class);
    }
});
