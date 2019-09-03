<?php

namespace App\Mail;

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
    public function __construct(Draw $draw, Participant $santa, $dearSantaLink = null)
    {
        $this->subject = __('emails.target_draw.title', ['draw' => $draw->id, 'subject' => $draw->email_title]);
        $this->content = str_replace('{SANTA}', $santa->name, str_replace('{TARGET}', $santa->target->name, $draw->email_body));
        $this->dearSantaLink = $dearSantaLink;
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
