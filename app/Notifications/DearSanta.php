<?php

namespace App\Notifications;

use App\Channels\TrackedMailChannel;
use App\Facades\DrawCrypt;
use App\Models\DearSanta as DearSantaModel;
use App\Models\Participant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class DearSanta extends Notification implements ShouldQueue, ShouldBeEncrypted
{
    use Queueable, SerializesModels;

    protected $dearSanta;

    public function __construct(DearSantaModel $dearSanta)
    {
        $this->dearSanta = $dearSanta;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param Participant $santa
     * @return array
     */
    public function via(Participant $santa)
    {
        return [TrackedMailChannel::class];
    }

    public function getMailableModel(Participant $santa)
    {
        return $this->dearSanta;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param Participant $santa
     * @return MailMessage
     */
    public function toMail(Participant $santa)
    {
        $url = URL::signedRoute('dearSanta', ['participant' => $santa->hash]).'#'.base64_encode(DrawCrypt::getIV());

        return (new MailMessage)
            ->subject(__('emails.dear_santa.title', ['draw' => $santa->draw->id]))
            ->view(['emails.dearsanta', 'emails.dearsanta_plain'], [
                'content' => $this->dearSanta->mail_body,
                'targetName' => $santa->target->name,
                'dearSantaLink' => $url
            ]);
    }
}
