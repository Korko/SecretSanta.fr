<?php

namespace App\Mail;

use App;
use App\Models\Participant;
use DrawCrypt;
use Illuminate\Support\Facades\URL;

class SuggestRedraw extends Mailable
{
    public $participantName;
    public $organizerName;
    public $targetName;

    public $acceptLink;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Participant $participant)
    {
        $this->subject = __('emails.organizer_recap_title', ['participant' => $participant->id]);

        $this->participantName = $participant->name;
        $this->organizerName = $participant->draw->organizer->name;
        $this->targetName = $participant->target->name;

        $this->acceptLink = URL::signedRoute('acceptRedraw', ['participant' => $participant->hash]).'#'.base64_encode(DrawCrypt::getIV());
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.suggest_redraw')
                    ->text('emails.suggest_redraw_plain');
    }
}
