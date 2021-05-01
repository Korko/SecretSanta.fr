<?php

namespace App\Notifications;

use App;
use App\Models\Participant;
use DrawCrypt;
//Illuminate/Contracts/Queue/ShouldBeEncrypted
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class OrganizerRecap extends Notification
{
    /**
     * Get the notification's delivery channels.
     *
     * @param  App\Models\Participant  $organizer
     * @return array
     */
    public function via(Participant $organizer)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  App\Models\Participant  $organizer
     * @return \Illuminate\Mail\Mailable
     */
    public function toMail(Participant $organizer)
    {
        return (new MailMessage)
            ->subject(__('emails.organizer_recap_title', ['draw' => $organizer->draw->id]))
            ->view('emails.organizer_recap', [
                'organizerName' => $organizer->name,
                'expirationDate' => $organizer->draw->expires_at->locale(App::getLocale())->isoFormat('LL'),
                'deletionDate' => $organizer->draw->deleted_at->locale(App::getLocale())->isoFormat('LL'),
                'nextSolvable' => $organizer->draw->next_solvable,
                'panelLink' => URL::signedRoute('organizerPanel', ['draw' => $organizer->draw->hash]).'#'.base64_encode(DrawCrypt::getKey()),
            ]);
    }
}
