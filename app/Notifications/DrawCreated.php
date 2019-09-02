<?php

namespace App\Notifications;

use App\Draw;
use App\Mail\Organizer as OrganizerEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Notification;
use Mail;

class DrawCreated extends Notification
{
    use Queueable;

    private $draw;

    public function __construct(Draw $draw)
    {
        $this->draw = $draw;
    }

    public function via(): array
    {
        return ['mail'];
    }

    public function toMail(): Mailable
    {
        $panelLink = route('organizerPanel', ['draw' => $this->draw->id]).'#'.base64_encode($this->draw->encryptionKey);

        return (new OrganizerEmail($this->draw, $panelLink));
    }
}
