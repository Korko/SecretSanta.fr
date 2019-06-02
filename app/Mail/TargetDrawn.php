<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TargetDrawn extends Mailable
{
    use Queueable, SerializesModels;

    public $santa;

    public $target;

    public $subject;

    public $content;

    public $dearSantaLink;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($santa, $target, $subject, $content, $dearSantaLink = null)
    {
        $this->santa = $santa;
        $this->target = $target;
        $this->subject = $subject;
        $this->content = $content;
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
