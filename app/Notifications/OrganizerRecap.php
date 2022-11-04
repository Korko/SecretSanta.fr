<?php

namespace App\Notifications;

use App\Mail\OrganizerRecap as OrganizerRecapMailable;
use App\Models\Draw;
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
     * @return array
     */
    public function via($organizer)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  \Illuminate\Notifications\AnonymousNotifiable|\App\Models\Participant  $organizer
     * @return \Illuminate\Mail\Mailable
     */
    public function toMail($organizer)
    {
        return (new OrganizerRecapMailable($this->draw))
            ->to($organizer->routeNotificationFor('mail'));
    }
}
