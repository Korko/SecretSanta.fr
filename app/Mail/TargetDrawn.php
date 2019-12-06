<?php

namespace App\Mail;

use Crypt;
use Hashids;
use App\Draw;
use App\Participant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Sichikawa\LaravelSendgridDriver\SendGrid;

class TargetDrawn extends Mailable
{
    use Queueable, SerializesModels, SendGrid;

    public $subject;

    public $content;
    public $dearSantaLink;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Participant $santa)
    {
        $this->subject = $this->parseKeywords(__('emails.target_draw.title', ['draw' => $santa->draw->id, 'subject' => $santa->draw->email_title]), $santa);
        $this->content = $this->parseKeywords($santa->draw->email_body, $santa);
        $this->dearSantaLink = route('dearsanta', ['santa' => Hashids::encode($santa->id)]).'#'.base64_encode(Crypt::getKey());
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
        return $this->subject($this->subject)
                    ->view('emails.target_drawn')
                    ->text('emails.target_drawn_plain');
    }
}
