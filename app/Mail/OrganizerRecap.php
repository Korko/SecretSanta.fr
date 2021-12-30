<?php

namespace App\Mail;

use App;
use App\Facades\DrawCrypt;
use App\Models\Draw;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\URL;
use Lang;

class OrganizerRecap extends Mailable
{
    protected $draw;

    /**
     * Create a new message instance.
     *
     * @param  \App\Models\Draw  $draw
     * @return void
     */
    public function __construct(Draw $draw)
    {
        $this->draw = $draw;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject(Lang::get('emails.organizer_recap_title', ['draw' => $this->draw->id]))
            ->view(['emails.organizer_recap', 'emails.organizer_recap_plain'], [
                'organizerName' => $this->draw->organizer_name,
                'expirationDate' => $this->draw->expires_at->locale(App::getLocale())->isoFormat('LL'),
                'deletionDate' => $this->draw->deleted_at->locale(App::getLocale())->isoFormat('LL'),
                'nextSolvable' => $this->draw->next_solvable,
                'panelLink' => URL::signedRoute('organizerPanel', ['draw' => $this->draw->hash]).'#'.base64_encode(DrawCrypt::getIV()),
            ]);
    }
}

