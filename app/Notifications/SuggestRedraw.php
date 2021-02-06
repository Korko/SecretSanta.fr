<?php

namespace App\Notifications;

use App\Channels\MailChannel;
use App\Mail\SuggestRedraw as SuggestRedrawMailable;
use App\Models\Participant;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
//Illuminate/Contracts/Queue/ShouldBeEncrypted

class SuggestRedraw extends Notification
{
    use Queueable;

    /**
     * Get the notification's delivery channels.
     *
     * @param  App\Models\Participant  $santa
     * @return array
     */
    public function via(Participant $santa)
    {
        return [MailChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  App\Models\Participant  $santa
     * @return \Illuminate\Mail\Mailable
     */
    public function toMail(Participant $santa)
    {
        return (new SuggestRedrawMailable($santa))
            ->to($santa);
    }
}
