<?php

namespace App\Mail;

use Log;
use Crypt;
use Hashids;
use App\Participant;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Mail\Mailer as MailerContract;

class TargetDrawn extends Mailable
{
    use Queueable, SerializesModels;

    public $content;
    public $participantId;
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
            'subject' => $santa->draw->email_title,
        ]), $santa);

        $this->content = $this->parseKeywords($santa->draw->email_body, $santa);

        $this->dearSantaLink = route('dearsanta', ['santa' => Hashids::encode($santa->id)]).'#'.base64_encode(Crypt::getKey());

        // Needed for the MessageSent event
        $this->participantId = $santa->id;
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

    public function send(MailerContract $mailer)
    {
        parent::send($mailer);

        $participant = Participant::find($this->participantId);
        $participant->delivery_status = Participant::SENT;
        $participant->save();
    }

    public function failed($exception)
    {
        $participant = Participant::find($this->participantId);
        $participant->delivery_status = Participant::ERROR;
        $participant->save();
    }
}
