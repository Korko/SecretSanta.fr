<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueueAfterCommit;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\Attributes\Backoff;
use Illuminate\Queue\Attributes\Tries;
use Illuminate\Support\Facades\Lang;

// Sent to the address a participant wants to move to: typing the code back on their page proves they own it
#[Backoff(30)]
#[Tries(10)]
final class ParticipantEmailCode extends Notification implements ShouldBeEncrypted, ShouldQueueAfterCommit
{
    use Queueable;

    public function __construct(private readonly string $name, private readonly string $code) {}

    /** @return list<string> */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(Lang::get('emails.participant_email_code.title', ['code' => $this->code]))
            ->view(['emails.participant_email_code', 'emails.participant_email_code_plain'], [
                'name' => $this->name,
                'code' => $this->code,
            ]);
    }
}
