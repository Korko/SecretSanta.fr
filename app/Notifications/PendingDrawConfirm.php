<?php

namespace App\Notifications;

use App\Mail\PendingDrawConfirm as PendingDrawConfirmMailable;
use App\Models\PendingDraw as PendingDrawModel;
use App\Models\PendingParticipant;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Notification;

class PendingDrawConfirm extends Notification
{
    public function __construct(
        protected readonly PendingDrawModel $draw
    ) {
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(AnonymousNotifiable|PendingParticipant $organizer): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(AnonymousNotifiable|PendingParticipant $organizer): Mailable
    {
        return (new PendingDrawConfirmMailable($this->draw))
            ->to($organizer->routeNotificationFor('mail'));
    }
}
