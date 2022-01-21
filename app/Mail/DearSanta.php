<?php

namespace App\Mail;

use App\Facades\DrawCrypt;
use App\Models\DearSanta as DearSantaModel;
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
    public function __construct(Participant $santa, DearSantaModel $dearSanta)
    {
        $this->santa = $santa;
        $this->dearSanta = $dearSanta;
    }

    protected function getMailable()
    {
        return $this->dearSanta;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $url = URL::signedRoute('santa.view', ['participant' => $this->santa->hash]).'#'.base64_encode(DrawCrypt::getIV());

        return $this
            ->subject(Lang::get('emails.dear_santa.title', ['draw' => $this->santa->draw->id]))
            ->markdown('emails.dearsanta', [
                'name' => $this->santa->name,
                'draw' => $this->santa->draw->id,
                'content' => $this->dearSanta->mail_body,
                'targetName' => $this->santa->target->name,
                'dearSantaLink' => $url
            ]);
    }
}
