<?php

namespace Korko\SecretSanta\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DearSanta extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $content;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $content)
    {
        $this->subject = $subject;
        $this->content = $content;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->subject)
                    ->view('emails.dearsanta')
                    ->text('emails.dearsanta_plain');
    }
}
