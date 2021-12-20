<?php

namespace App\Mail;

use App\Facades\DrawCrypt;
use App\Models\DearSanta;
use App\Models\Participant;
use Illuminate\Support\Facades\URL;
use Lang;

class DearSanta extends TrackedMailable
{
    protected $santa;
    protected $dearSanta;

    /**
     * Create a new message instance.
     *
     * @param  \App\Models\Participant  $santa
     * @param  \App\Models\DearSanta  $dearSanta
     * @return void
     */
    public function __construct(Participant $santa, DearSanta $dearSanta)
    {
        $this->santa = $santa;
        $this->dearSanta = $dearSanta;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $url = URL::signedRoute('dearSanta', ['participant' => $this->santa->hash]).'#'.base64_encode(DrawCrypt::getIV());

        return $this
            ->subject(Lang::get('emails.dear_santa.title', ['draw' => $this->santa->draw->id]))
            ->view(['emails.dearsanta', 'emails.dearsanta_plain'], [
                'content' => $this->dearSanta->mail_body,
                'targetName' => $this->santa->target->name,
                'dearSantaLink' => $url
            ]);
    }
}
