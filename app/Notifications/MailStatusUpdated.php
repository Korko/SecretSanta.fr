<?php

namespace App\Notifications;

use App\Events\MailStatusUpdated as Event;
use App\Models\Mail as MailModel;
use App\Models\Participant;
//Illuminate/Contracts/Queue/ShouldBeEncrypted
use Illuminate\Notifications\Notification;

class MailStatusUpdated extends Notification
{
    public function __construct(MailModel $mail)
    {
        Event::dispatch($mail);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  App\Models\Participant  $santa
     * @return array
     */
    public function via(Participant $santa)
    {
        return [];
    }
}
