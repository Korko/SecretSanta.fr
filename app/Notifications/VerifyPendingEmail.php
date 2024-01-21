<?php

namespace App\Notifications;

use App\Models\Participant;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Notification;

class VerifyPendingEmail extends Notification
{
    /**
     * Get the notification's delivery channels.
     */
    public function via(Participant $participant): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(Participant $participant): Mailable
    {
        //
    }
}
