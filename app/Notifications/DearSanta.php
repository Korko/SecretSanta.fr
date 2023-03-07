<?php

namespace App\Notifications;

use App\Mail\DearSanta as DearSantaMailable;
use App\Models\DearSanta as DearSantaModel;
use App\Models\Participant;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Notification;

class DearSanta extends Notification
{
    protected $dearSanta;

    public function __construct(DearSantaModel $dearSanta)
    {
        $this->dearSanta = $dearSanta;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(Participant $santa): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(Participant $santa): Mailable
    {
        return (new DearSantaMailable($santa, $this->dearSanta))
            ->to($santa->routeNotificationFor('mail'));
    }
}
