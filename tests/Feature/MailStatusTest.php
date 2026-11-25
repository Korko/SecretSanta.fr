<?php

declare(strict_types=1);

use App\Channels\TrackedMailChannel;
use App\Enums\DeliveryStatus;
use App\Events\MailStatusUpdated;
use App\Models\Draw;
use App\Models\Mail;
use App\Notifications\TargetDrawn;
use Illuminate\Contracts\Mail\Factory as MailFactory;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Mail\Markdown;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;

it('marks a mail as failed whatever stopped its sending, not only the SMTP session', function (): void {
    Notification::fake();
    $participant = Draw::factory()->hasParticipants(3)->create()->participants->first();

    $mailer = Mockery::mock(Mailer::class);
    $mailer->shouldReceive('send')->andThrow(new RuntimeException('DKIM key unreadable'));
    $factory = Mockery::mock(MailFactory::class);
    $factory->shouldReceive('mailer')->andReturn($mailer);

    expect(fn (): mixed => (new TrackedMailChannel($factory, resolve(Markdown::class)))->send($participant, new TargetDrawn))
        ->toThrow(RuntimeException::class, 'DKIM key unreadable');

    expect($participant->mail->fresh()->delivery_status)->toBe(DeliveryStatus::Error);
});

it('does not overwrite a reception written meanwhile by another instance', function (): void {
    Notification::fake();
    $participant = Draw::factory()->hasParticipants(3)->create()->participants->first();

    // The worker's instance, kept during the whole SMTP session
    $stale = Mail::query()->findOrFail($participant->mail->id);
    $stale->markAsSending();

    // Meanwhile, the recipient opens their page
    Mail::query()->findOrFail($participant->mail->id)->markAsReceived();

    Event::fake([MailStatusUpdated::class]);
    $stale->markAsSent();
    $stale->markAsDelayed();
    $stale->markAsSending();

    expect($participant->mail->fresh()->delivery_status)->toBe(DeliveryStatus::Received)
        ->and($stale->delivery_status)->toBe(DeliveryStatus::Received);
    Event::assertNotDispatched(MailStatusUpdated::class);
});

it('broadcasts a status change only once its transaction is committed', function (): void {
    Notification::fake();
    $participant = Draw::factory()->hasParticipants(3)->create()->participants->first();
    Event::fake([MailStatusUpdated::class]);

    DB::transaction(function () use ($participant): void {
        $participant->mail->markAsCreated();
        Event::assertNotDispatched(MailStatusUpdated::class);
    });

    Event::assertDispatched(MailStatusUpdated::class);
});
