<?php

namespace App\Notifications;

use App\Mail\TargetDrawn as TargetDrawnMailable;
use App\Models\Participant;
use Illuminate\Notifications\Notification;

class TargetDrawn extends Notification
{
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

    /**
     * Get the mail representation of the notification.
     *
     * @param  \App\Models\Participant  $santa
     * @return \Illuminate\Mail\Mailable
     */
    public function toMail(Participant $santa)
    {
        return (new TargetDrawnMailable($santa))
            ->to($santa->routeNotificationFor('mail'));
    }
}
