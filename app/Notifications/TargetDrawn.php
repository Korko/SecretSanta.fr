<?php

namespace App\Notifications;

use App\Mail\TargetDrawn as TargetDrawnMailable;
use App\Models\Participant;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Notification;

class TargetDrawn extends Notification
{
    /**
     * Get the notification's delivery channels.
     */
    public function via(Participant $santa): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(Participant $santa): Mailable
    {
        return (new TargetDrawnMailable($santa))
            ->to($santa->routeNotificationFor('mail'));
    }
}
