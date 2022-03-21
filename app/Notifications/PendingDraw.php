<?php

namespace App\Notifications;

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
     *
     * @param  \Illuminate\Notifications\AnonymousNotifiable $organizer
     * @return array
     */
    public function via($organizer)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  \Illuminate\Notifications\AnonymousNotifiable $organizer
     * @return \Illuminate\Mail\Mailable
     */
    public function toMail($organizer)
    {
        return (new PendingDrawMailable($this->draw))
            ->to($organizer->routeNotificationFor('mail'));
    }
}