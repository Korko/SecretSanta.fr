<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Organizer extends Mailable
{
    use Queueable, SerializesModels;

    public $panelLink;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($panelLink)
    {
        $this->panelLink = $panelLink;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('todo')
                    ->view('emails.organizer')
                    ->text('emails.organizer_plain');
    }
}
