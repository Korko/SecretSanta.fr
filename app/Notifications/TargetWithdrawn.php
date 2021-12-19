<?php

namespace App\Notifications;

use App\Channels\MailChannel;
use App\Models\Participant;
use DrawCrypt;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;
use Lang;

class TargetWithdrawn extends Notification implements ShouldQueue, ShouldBeEncrypted
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
     *
     * @param  \App\Models\Participant  $santa
     * @return array
     */
    public function via(Participant $santa)
    {
        return ['mail'];
    }

    public function getMailableModel(Participant $santa)
    {
        return $santa;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  \App\Models\Participant  $santa
     * @return \Illuminate\Mail\Mailable
     */
    public function toMail(Participant $santa)
    {
        $title = Lang::get('emails.target_withdrawn.title', [
            'draw' => $santa->draw->id
        ]);

        return (new MailMessage)
            ->subject($title)
            ->view(['emails.target_withdrawn', 'emails.target_withdrawn_plain'], [
                'santaName' => $santa->name,
                'targetName' => $santa->target->name,
            ]);
    }
}
