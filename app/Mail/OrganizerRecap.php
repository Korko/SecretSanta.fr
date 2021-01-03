<?php

namespace App\Mail;

use App;
use App\Models\Draw;
use DrawCrypt;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\URL;

class OrganizerRecap extends Mailable
{
    use Queueable;

    public $organizerName;
    public $expirationDate;
    public $deletionDate;
    public $panelLink;
    public $nextSolvable;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Draw $draw)
    {
        $this->subject = __('emails.organizer_recap_title', ['draw' => $draw->id]);

        $this->organizerName = $draw->organizer->name;

        $this->expirationDate = $draw->expires_at->locale(App::getLocale())->isoFormat('LL');
        $this->deletionDate = $draw->deleted_at->locale(App::getLocale())->isoFormat('LL');

        $this->nextSolvable = $draw->next_solvable;

        $this->panelLink = URL::signedRoute('organizerPanel', ['draw' => $draw->hash]).'#'.base64_encode(DrawCrypt::getKey());
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.organizer_recap')
                    ->text('emails.organizer_recap_plain');
    }
}
