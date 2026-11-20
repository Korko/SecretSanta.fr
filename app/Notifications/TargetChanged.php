<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Participant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueueAfterCommit;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\Attributes\Backoff;
use Illuminate\Queue\Attributes\Tries;
use Illuminate\Support\Facades\Lang;

// Not tracked on purpose: the santa's row on the organizer panel would start over right after a change of
// participants, and show who now gives to whom (the newcomer's santa, for one)
#[Backoff(30)]
#[Tries(10)]
final class TargetChanged extends Notification implements ShouldBeEncrypted, ShouldQueueAfterCommit
{
    use Queueable;

    /**
     * Get the notification's delivery channels.
     */
    /** @return list<string> */
    public function via(Participant $santa): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(Participant $santa): MailMessage
    {
        $title = Lang::get('emails.target_changed.title', [
            'draw' => $santa->draw->id,
        ]);

        return (new MailMessage)
            ->subject($title)
            ->view(['emails.target_changed', 'emails.target_changed_plain'], [
                'santaName' => $santa->name,
                'targetName' => $santa->target->name,
            ]);
    }
}
