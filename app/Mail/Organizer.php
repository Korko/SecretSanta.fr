<?php

namespace App\Mail;

use App\Draw;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Organizer extends Mailable
{
    use Queueable, SerializesModels;

    public $draw;
    public $panelLink;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Draw $draw, $panelLink)
    {
        $this->draw = $draw;
        $this->panelLink = $panelLink;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject(__('emails.organizer.title', ['draw' => $this->draw->id]))
                    ->view('emails.organizer')
                    ->text('emails.organizer_plain');
    }
}
