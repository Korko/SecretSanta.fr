<?php

namespace App\Mail;

use App\Participant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DearSanta extends Mailable
{
    use Queueable, SerializesModels;

    public $santa;

    public $content;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Participant $santa, $content)
    {
        $this->santa = $santa;
        $this->content = $content;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject(__('emails.dear_santa.title', ['draw' => $this->santa->draw->id]))
                    ->view('emails.dearsanta')
                    ->text('emails.dearsanta_plain');
    }
}
