<?php

namespace App\Notifications;

use App\Models\Participant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

class TargetWithdrawn extends Notification implements ShouldBeEncrypted, ShouldQueue
{
    use Queueable;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 10;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $backoff = 30;

    /**
     * Get the notification's delivery channels.
     */
    public function via(Participant $santa): array
    {
        return ['mail'];
    }

    public function getMailableModel(Participant $santa)
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
