<?php

namespace App\Notifications;

use App\Channels\MailChannel;
use App\Models\Participant;
use DrawCrypt;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;
use Lang;

// Should NOT be Queued! Notifiable is no more in database.
class ConfirmWithdrawal extends Notification
{
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

    public function getMailableModel(Participant $santa)
    {
        return $santa;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  \App\Models\Participant  $santa
     * @return \Illuminate\Mail\Mailable
     */
    public function toMail(Participant $santa)
    {
        $title = Lang::get('emails.confirm_withdrawal.title', [
            'draw' => $santa->draw->id
        ]);

        return (new MailMessage)
            ->subject($title)
            ->view('emails.confirm_withdrawal', [
                'santaName' => $santa->name,
                'organizerName' => $santa->draw->organizer_name,
            ]);
    }
}
