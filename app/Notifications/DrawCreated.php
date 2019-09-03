<?php

namespace App\Notifications;

use App\Mail\Organizer as OrganizerEmail;
use App\Participant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Notification;

class DrawCreated extends Notification
{
    use Queueable;

    public function via(): array
    {
        return ['mail'];
    }

    public function toMail(Participant $organizer): Mailable
    {
        $panelLink = route('organizerPanel', ['draw' => $organizer->draw_id]).'#'.base64_encode($organizer->encryptionKey);

        return (new OrganizerEmail($organizer->draw, $panelLink));
    }
}
