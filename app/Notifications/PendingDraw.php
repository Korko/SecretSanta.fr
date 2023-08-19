<?php

namespace App\Notifications;

use App\Mail\PendingDraw as PendingDrawMailable;
use App\Models\PendingDraw as PendingDrawModel;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Notification;

class PendingDraw extends Notification
{
    public function __construct(
        protected readonly PendingDrawModel $draw
    ) {
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(AnonymousNotifiable $organizer): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(AnonymousNotifiable $organizer): Mailable
    {
        return (new PendingDrawMailable($this->draw))
            ->to($organizer->routeNotificationFor('mail'));
    }
}
