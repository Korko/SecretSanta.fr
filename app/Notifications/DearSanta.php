<?php

namespace App\Notifications;

use App\Channels\MailChannel;
use App\Models\DearSanta as DearSantaModel;
use App\Mail\DearSanta as DearSantaMailable;
use App\Models\Participant;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
//Illuminate/Contracts/Queue/ShouldBeEncrypted

class DearSanta extends Notification
{
    use Queueable;

    protected $dearSanta;

    public function __construct(DearSantaModel $dearSanta)
    {
        $this->dearSanta = $dearSanta;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  App\Models\Participant  $santa
     * @return array
     */
    public function via(Participant $santa)
    {
        return [MailChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  App\Models\Participant  $santa
     * @return \Illuminate\Mail\Mailable
     */
    public function toMail(Participant $santa)
    {
        return (new DearSantaMailable($this->dearSanta))
            ->to($santa);
    }
}
