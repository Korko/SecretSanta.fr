<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Participant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\Attributes\Backoff;
use Illuminate\Queue\Attributes\Tries;
use Illuminate\Support\Facades\Lang;

#[Backoff(30)]
#[Tries(10)]
final class TargetWithdrawn extends Notification implements ShouldBeEncrypted, ShouldQueue, TrackedNotification
{
    use Queueable;

    /**
     * Get the notification's delivery channels.
     */
    public function via(Participant $santa): array
    {
        return ['mail'];
    }

    public function getMailableModel(Participant $santa): Participant
    {
        return $santa;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(Participant $santa): MailMessage
    {
        $title = Lang::get('emails.target_withdrawn.title', [
            'draw' => $santa->draw->id,
        ]);

        return (new MailMessage)
            ->subject($title)
            ->view(['emails.target_withdrawn', 'emails.target_withdrawn_plain'], [
                'santaName' => $santa->name,
                'targetName' => $santa->target->name,
            ]);
    }
}
