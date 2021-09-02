<?php

namespace App\Notifications;

use App\Models\DearSanta as DearSantaModel;
use App\Models\Participant;
use App\Channels\TrackedMailChannel;
//Illuminate/Contracts/Queue/ShouldBeEncrypted
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DearSanta extends Notification
{
    protected $dearSanta;

    public function __construct(DearSantaModel $dearSanta)
    {
        $this->dearSanta = $dearSanta;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  App\Models\Participant  $santa
     * @return array
     */
    public function via(Participant $santa)
    {
        return [TrackedMailChannel::class];
    }

    public function getMailableModel(Participant $santa)
    {
        return $this->dearSanta;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  App\Models\Participant  $santa
     * @return \Illuminate\Mail\Mailable
     */
    public function toMail(Participant $santa)
    {
        return (new MailMessage)
            ->subject(__('emails.dear_santa.title', ['draw' => $santa->draw->id]))
            ->view('emails.dearsanta', [
                'content' => $this->dearSanta->mail_body,
                'targetName' => $santa->target->name
            ]);
    }
}
