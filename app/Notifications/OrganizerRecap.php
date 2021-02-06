<?php

namespace App\Notifications;

use App\Channels\MailChannel;
use App\Mail\OrganizerRecap as OrganizerRecapMailable;
use App\Models\Participant;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
//Illuminate/Contracts/Queue/ShouldBeEncrypted

class OrganizerRecap extends Notification
{
    use Queueable;

    /**
     * Get the notification's delivery channels.
     *
     * @param  App\Models\Participant  $organizer
     * @return array
     */
    public function via(Participant $organizer)
    {
        return [MailChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  App\Models\Participant  $organizer
     * @return \Illuminate\Mail\Mailable
     */
    public function toMail(Participant $organizer)
    {
        return (new OrganizerRecapMailable($organizer->draw))
            ->to($organizer);
    }
}
