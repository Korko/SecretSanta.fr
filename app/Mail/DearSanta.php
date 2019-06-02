<?php

namespace App\Mail;

use App\Draw;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DearSanta extends Mailable
{
    use Queueable, SerializesModels;

    public $draw;

    public $content;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Draw $draw, $content)
    {
        $this->content = $content;
        $this->draw = $draw;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject(__('emails.dear_santa.title', ['draw' => $this->draw->id]))
                    ->view('emails.dearsanta')
                    ->text('emails.dearsanta_plain');
    }
}
