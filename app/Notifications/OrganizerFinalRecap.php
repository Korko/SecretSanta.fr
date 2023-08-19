<?php

namespace App\Notifications;

use App\Mail\OrganizerFinalRecap as OrganizerFinalRecapMailable;
use App\Models\Draw;
use App\Models\Participant;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Notification;

class OrganizerFinalRecap extends Notification
{
    public function __construct(
        protected readonly Draw $draw
    ) {
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
        return (new OrganizerFinalRecapMailable($this->draw))
            ->to($organizer);
    }
}
