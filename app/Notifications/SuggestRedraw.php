<?php

namespace App\Notifications;

use App\Models\Participant;
use DrawCrypt;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class SuggestRedraw extends Notification
{
    /**
     * Get the notification's delivery channels.
     *
     * @param Participant $participant
     * @return array
     */
    public function via(Participant $participant)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param Participant $santa
     * @return MailMessage
     */
    public function toMail(Participant $participant)
    {
        return (new MailMessage)
            ->subject(__('emails.suggest_redraw_title', ['participant' => $participant->id]))
            ->view('emails.suggest_redraw', [
                'participantName' => $participant->name,
                'organizerName' => $participant->draw->organizer_name,
                'targetName' => $participant->target->name,
                'acceptLink' => URL::signedRoute('acceptRedraw', ['participant' => $participant->hash]).'#'.base64_encode(DrawCrypt::getIV()),
            ]);
    }
}
