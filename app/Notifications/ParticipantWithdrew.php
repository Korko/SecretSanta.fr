<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Draw;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueueAfterCommit;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\Attributes\Backoff;
use Illuminate\Queue\Attributes\Tries;
use Illuminate\Support\Facades\Lang;

// Sent to the organizer when a participant leaves the draw from their own page (their santa got a new target)
#[Backoff(30)]
#[Tries(10)]
final class ParticipantWithdrew extends Notification implements ShouldBeEncrypted, ShouldQueueAfterCommit
{
    use Queueable;

    public function __construct(private readonly string $name) {}

    /** @return list<string> */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $draw = $notifiable instanceof Draw ? $notifiable : null;

        return (new MailMessage)
            ->subject(Lang::get('emails.participant_withdrew.title', ['draw' => $draw?->id, 'name' => $this->name]))
            ->view(['emails.participant_withdrew', 'emails.participant_withdrew_plain'], [
                'organizerName' => $draw?->organizer_name,
                'name' => $this->name,
            ]);
    }
}
