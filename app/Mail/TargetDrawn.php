<?php

namespace App\Mail;

use App\Models\Mail as MailModel;
use App\Models\Participant;
use Crypt;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\URL;

class TargetDrawn extends TrackedMailable
{
    use Queueable;

    protected $santa;

    public $content;
    public $dearSantaLink;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Participant $santa)
    {
        $this->santa = $santa;

        $this->subject = $this->parseKeywords(__('emails.target_draw.title', [
            'draw' => $santa->draw->id,
            'subject' => $santa->draw->mail_title,
        ]), $santa);

        $this->content = $this->parseKeywords($santa->draw->mail_body, $santa);

        $this->dearSantaLink = URL::signedRoute('dearsanta', ['participant' => $santa->hash]).'#'.base64_encode(Crypt::getKey());

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
