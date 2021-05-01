<?php

namespace App\Notifications;

use App\Models\Participant;
use App\Channels\TrackedMailChannel;
use DrawCrypt;
//Illuminate/Contracts/Queue/ShouldBeEncrypted
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;
use Lang;

class TargetDrawn extends Notification
{
    /**
     * Get the notification's delivery channels.
     *
     * @param  App\Models\Participant  $santa
     * @return array
     */
    public function via(Participant $santa)
    {
        return [TrackedMailChannel::class];
    }

    public function getMailableModel(Participant $santa)
    {
        return $santa;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  App\Models\Participant  $santa
     * @return \Illuminate\Mail\Mailable
     */
    public function toMail(Participant $santa)
    {
        $title = $this->parseKeywords(Lang::get('emails.target_draw.title', [
            'draw' => $santa->draw->id,
            'subject' => $santa->draw->mail_title,
        ]), $santa);

        $content = $this->parseKeywords($santa->draw->mail_body, $santa);

        $url = URL::signedRoute('dearsanta', ['participant' => $santa->hash]).'#'.base64_encode(DrawCrypt::getKey());

        return (new MailMessage)
            ->subject($title)
            ->view('emails.target_drawn', [
                'content' => $content,
                'dearSantaLink' => $url,
            ]);
    }

    protected function parseKeywords($str, Participant $santa)
    {
        return str_replace(['{SANTA}', '{TARGET}'], [$santa->name, $santa->target->name], $str);
    }
}
