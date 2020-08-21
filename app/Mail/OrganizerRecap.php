<?php

namespace App\Mail;

use App;
use App\Models\Draw;
use Crypt;
use Illuminate\Bus\Queueable;

class OrganizerRecap extends Mailable
{
    use Queueable;

    public $organizerName;
    public $expirationDate;
    public $panelLink;

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

        $this->panelLink = route('organizerPanel', ['draw' => $draw->hash]).'#'.base64_encode(Crypt::getKey());
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
