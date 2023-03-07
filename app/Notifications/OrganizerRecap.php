<?php

namespace App\Notifications;

use App\Mail\OrganizerRecap as OrganizerRecapMailable;
use App\Models\Draw;
use App\Models\Participant;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\AnonymousNotifiable;
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
     */
    public function via(AnonymousNotifiable|Participant $organizer): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(AnonymousNotifiable|Participant $organizer): Mailable
    {
        return (new OrganizerRecapMailable($this->draw))
            ->to($organizer);
    }
}
