<?php

namespace App\Notifications;

use App;
use App\Channels\MailChannel;
use App\Facades\DrawCrypt;
use App\Models\Participant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class OrganizerRecap extends Notification implements ShouldQueue, ShouldBeEncrypted
{
    use Queueable;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 20;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $backoff = 30;

    /**
     * Get the notification's delivery channels.
     *
     * @param  App\Models\Participant  $organizer
     * @return array
     */
    public function via(Participant $organizer)
    {
        return [MailChannel::class];
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
                'panelLink' => URL::signedRoute('organizerPanel', ['draw' => $organizer->draw->hash]).'#'.base64_encode(DrawCrypt::getIV()),
            ]);
    }
}
