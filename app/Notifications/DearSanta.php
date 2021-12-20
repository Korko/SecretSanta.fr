<?php

namespace App\Notifications;

use App\Channels\TrackedMailChannel;
use App\Mail\DearSanta as DearSantaMailable;
use App\Models\DearSanta as DearSantaModel;
use App\Models\Participant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

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
     * @param  \App\Models\Participant  $santa
     * @return array
     */
    public function via(Participant $santa)
    {
        return [TrackedMailChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  \App\Models\Participant  $santa
     * @return \Illuminate\Mail\Mailable
     */
    public function toMail(Participant $santa)
    {
        return new DearSantaMailable($santa, $this->dearSanta);
    }
}
