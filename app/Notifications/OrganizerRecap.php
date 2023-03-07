<?php

namespace App\Notifications;

use App\Mail\OrganizerRecap as OrganizerRecapMailable;
use App\Models\Draw;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Notification;

class OrganizerRecap extends Notification
{
    protected $draw;

    public function __construct(Draw $draw)
    {
        $this->draw = $draw;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  \Illuminate\Notifications\AnonymousNotifiable|\App\Models\Participant  $organizer
     */
    public function via($organizer): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  \Illuminate\Notifications\AnonymousNotifiable|\App\Models\Participant  $organizer
     */
    public function toMail($organizer): Mailable
    {
        return (new OrganizerRecapMailable($this->draw))
            ->to($organizer->routeNotificationFor('mail'));
    }
}
