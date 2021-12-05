<?php

namespace App\Notifications;

use App\Channels\MailChannel;
use App\Facades\DrawCrypt;
use App\Models\Participant;
//Illuminate/Contracts/Queue/ShouldBeEncrypted
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class SuggestRedraw extends Notification
{
    /**
     * Get the notification's delivery channels.
     *
     * @param  \App\Models\Participant  $participant
     * @return array
     */
    public function via(Participant $participant)
    {
        return [MailChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  \App\Models\Participant  $santa
     * @return \Illuminate\Mail\Mailable
     */
    public function toMail(Participant $participant)
    {
        return (new MailMessage)
            ->subject(__('emails.suggest_redraw_title', ['participant' => $participant->id]))
            ->view('emails.suggest_redraw', [
                'participantName' => $participant->name,
                'organizerName' => $participant->draw->organizer->name,
                'targetName' => $participant->target->name,
                'acceptLink' => URL::signedRoute('acceptRedraw', ['participant' => $participant->hash]).'#'.base64_encode(DrawCrypt::getIV()),
            ]);
    }
}
