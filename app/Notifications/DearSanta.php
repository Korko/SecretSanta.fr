<?php

namespace App\Notifications;

use App\Mail\DearSanta as DearSantaMailable;
use App\Models\DearSanta as DearSantaModel;
use App\Models\Participant;
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
     *
     * @param  \App\Models\Participant  $santa
     * @return array
     */
    public function via(Participant $santa)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  \App\Models\Participant  $santa
     * @return \Illuminate\Mail\Mailable
     */
    public function toMail(Participant $santa)
    {
        return (new DearSantaMailable($santa, $this->dearSanta))
            ->to($santa->routeNotificationFor('mail'));
    }
}
