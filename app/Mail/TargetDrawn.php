<?php

namespace App\Mail;

use App\Facades\DrawCrypt;
use App\Models\Participant;
use Illuminate\Support\Facades\URL;
use Lang;

class TargetDrawn extends TrackedMailable
{
    protected $santa;

    /**
     * Create a new message instance.
     *
     * @param  \App\Models\Participant  $santa
     * @return void
     */
    public function __construct(Participant $santa)
    {
        $this->santa = $santa;
    }

    protected function getMailable()
    {
        return $this->santa;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $title = $this->parseKeywords(Lang::get('emails.target_draw.title', [
            'draw' => $this->santa->draw->id,
            'subject' => $this->santa->draw->mail_title,
        ]), $this->santa);

        $content = $this->parseKeywords($this->santa->draw->mail_body, $this->santa);

        $url = URL::signedRoute('dearSanta', ['participant' => $this->santa->hash]).'#'.base64_encode(DrawCrypt::getIV());

        return $this
            ->subject($title)
            ->view(['emails.target_drawn', 'emails.target_drawn_plain'], [
                'content' => $content,
                'dearSantaLink' => $url,
            ]);
    }

    /**
     * Replace special keywords with participants names
     *
     * @param  string  $str
     * @param  \App\Models\Participant  $santa
     * @return string
     */
    protected function parseKeywords($str, Participant $santa)
    {
        return str_replace(['{SANTA}', '{TARGET}'], [$santa->name, $santa->target->name], $str);
    }
}
