<?php

declare(strict_types=1);

use App\Models\Draw;
use App\Models\Mail;
use App\Models\Participant;
use App\Services\ParticipantsEditor;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

// Two simultaneous requests can't be run in a test: the second one is replayed with what it had read before the first

it('gives the resend slot of an email to a single request', function (): void {
    Notification::fake();

    $draw = Draw::factory()->hasParticipants(3)->create();
    $mail = $draw->participants->first()->mail;
    $mail->updated_at = $mail->updated_at->subSeconds(config()->integer('mail.resend_delay'));
    $mail->save();

    $stale = Mail::query()->findOrFail($mail->id);
    expect($mail->claimResend(config()->integer('mail.resend_delay')))->toBeTrue()
        ->and($stale->claimResend(config()->integer('mail.resend_delay')))->toBeFalse();
});

it('refuses to remove a participant another change removed in the meantime, with a message', function (): void {
    Notification::fake();

    $draw = Draw::factory()->hasParticipants(5)->create();
    $participant = $draw->participants->first();

    new ParticipantsEditor($draw)->apply([$participant], []);

    expect(fn () => new ParticipantsEditor($draw)->apply([$participant], []))
        ->toThrow(ValidationException::class, trans('organizer.already_removed', ['name' => $participant->name]));
});

it('checks the names again once the draw is locked', function (): void {
    Notification::fake();

    $draw = Draw::factory()->hasParticipants(4)->create();

    // Both tabs validated "Zoé" against the same participants
    new ParticipantsEditor($draw)->apply([], [['name' => 'Zoé', 'email' => 'zoe@example.com']]);

    expect(fn () => new ParticipantsEditor($draw)->apply([], [['name' => 'Zoé', 'email' => 'zoe2@example.com']]))
        ->toThrow(ValidationException::class);
    expect($draw->participants()->get()->where('name', 'Zoé'))->toHaveCount(1);
});

it('refuses to bring back a withdrawn participant whose name was taken in the meantime', function (): void {
    Notification::fake();

    $draw = Draw::factory()->hasParticipants(4)->create();
    $participant = $draw->participants->first();
    new ParticipantsEditor($draw)->withdraw($participant);
    new ParticipantsEditor($draw)->apply([], [['name' => $participant->name, 'email' => 'other@example.com']]);

    expect(fn () => new ParticipantsEditor($draw)->apply([], [], [Participant::query()->findOrFail($participant->id)]))
        ->toThrow(ValidationException::class);
});

it('keeps a single recap mail per draw', function (): void {
    Notification::fake();

    $draw = Draw::factory()->hasParticipants(3)->create();
    $draw->recapMail()->delete();

    // Both requests saw a draw without a recap mail
    $first = Draw::query()->findOrFail($draw->id);
    $second = Draw::query()->findOrFail($draw->id);
    $first->load('mail');
    $second->load('mail');

    expect($first->recapMail()->id)->toBe($second->recapMail()->id)
        ->and(Mail::query()->whereMorphedTo('mailable', $draw)->count())->toBe(1);
});
