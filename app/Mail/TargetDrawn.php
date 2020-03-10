<?php

namespace App\Mail;

use App\Participant;
use Crypt;
use Hashids;
use Illuminate\Bus\Queueable;

class TargetDrawn extends TrackedMailable
{
    use Queueable;

    public $content;
    public $dearSantaLink;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Participant $santa)
    {
        $this->subject = $this->parseKeywords(__('emails.target_draw.title', [
            'draw' => $santa->draw->id,
            'subject' => $santa->draw->mail_title,
        ]), $santa);

        $this->content = $this->parseKeywords($santa->draw->mail_body, $santa);

        $this->dearSantaLink = route('dearsanta', ['santa' => Hashids::encode($santa->id)]).'#'.base64_encode(Crypt::getKey());

        $this->track($santa->mail);
    }

    protected function parseKeywords($str, Participant $santa)
    {
        return str_replace(['{SANTA}', '{TARGET}'], [$santa->name, $santa->target->name], $str);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.target_drawn')
                    ->text('emails.target_drawn_plain');
    }
}
