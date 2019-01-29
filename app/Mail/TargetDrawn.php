<?php

namespace App\Mail;

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
    public function __construct($subject, $content, $santaName, $targetName, $dearSantaLink = null)
    {
        $this->subject = $subject;
        $this->content = str_replace('{SANTA}', $santaName, str_replace('{TARGET}', $targetName, $content));
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
