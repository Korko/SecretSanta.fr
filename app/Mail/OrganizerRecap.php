<?php

namespace App\Mail;

use Crypt;
use App\Draw;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;

class OrganizerRecap extends Mailable
{
    use Queueable, SerializesModels;

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

        $this->expirationDate = $draw->expires_at;

        $this->panelLink = route('organizerPanel', ['draw' => $draw->id]).'#'.base64_encode(Crypt::getKey());
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
