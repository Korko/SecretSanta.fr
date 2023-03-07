<?php

namespace App\Notifications;

use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Mail\Mailable;
use App\Mail\PendingDraw as PendingDrawMailable;
use App\Models\PendingDraw as PendingDrawModel;
use Illuminate\Notifications\Notification;

class PendingDraw extends Notification
{
    protected $draw;

    public function __construct(PendingDrawModel $draw)
    {
        $this->draw = $draw;
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
