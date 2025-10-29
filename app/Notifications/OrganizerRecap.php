<?php

namespace App\Notifications;

use App;
use App\Channels\MailChannel;
use App\Models\Draw;
use App\Models\Participant;
use DrawCrypt;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class OrganizerRecap extends Notification implements ShouldQueue, ShouldBeEncrypted
{
    use Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 20;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     *
     * @var int
     */
    public $maxExceptions = 10;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $backoff = 30;

    protected $draw;

    public function __construct(Draw $draw)
    {
        $this->draw = $draw;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param AnonymousNotifiable|Participant $organizer
     * @return array
     */
    public function via($organizer)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param AnonymousNotifiable|Participant $organizer
     * @return Mailable
     */
    public function toMail($organizer)
    {
        return (new MailMessage)
            ->subject(__('emails.organizer_recap_title', ['draw' => $this->draw->id]))
            ->view(['emails.organizer_recap', 'emails.organizer_recap_plain'], [
                'organizerName' => $this->draw->organizer_name,
                'expirationDate' => $this->draw->expires_at->locale(App::getLocale())->isoFormat('LL'),
                'deletionDate' => $this->draw->deleted_at->locale(App::getLocale())->isoFormat('LL'),
                'nextSolvable' => $this->draw->next_solvable,
                'panelLink' => URL::signedRoute('organizerPanel', ['draw' => $this->draw->hash]).'#'.base64_encode(DrawCrypt::getIV()),
            ])
            ->attachData(
                "\xEF\xBB\xBF".// UTF-8 BOM
                $this->draw->participants
                    ->toCsv(['name', 'email', 'exclusionsNames'])
                    ->prepend([
                            ['# Fichier généré le '.date('d-m-Y').' sur '.config('app.name').' ('.config('app.url').')'],
                            ['# Ce fichier peut être utilisé pour préremplir les participants ainsi que les exclusions associées'],
                    ]),
            'secretsanta_'.$this->draw->expires_at->isoFormat('YYYY-MM-DD').'_init.csv', [
                'mime' => 'text/csv',
            ]);
    }
}
