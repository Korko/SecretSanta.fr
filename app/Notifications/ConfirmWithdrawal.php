<?php

namespace App\Notifications;

use Illuminate\Mail\Mailable;
use App\Mail\ConfirmWithdrawal as ConfirmWithdrawalMailable;
use App\Models\Participant;
use Illuminate\Notifications\Notification;

// Should NOT be Queued! Notifiable is no more in database.
class ConfirmWithdrawal extends Notification
{
    /**
     * Get the notification's delivery channels.
     *
     * @param  \App\Models\Participant  $santa
     * @return array
     */
    public function via(Participant $santa): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  \App\Models\Participant  $santa
     * @return \Illuminate\Mail\Mailable
     */
    public function toMail(Participant $santa): Mailable
    {
        return (new ConfirmWithdrawalMailable($santa))
            ->to($santa->routeNotificationFor('mail'));
    }
}
