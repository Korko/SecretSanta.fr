<?php

namespace App\Mail;

use App\Participant;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;

class DearSanta extends Mailable
{
    use Queueable, SerializesModels;

    public $targetName;
    public $content;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Participant $santa, $content)
    {
        $this->subject = __('emails.dear_santa.title', ['draw' => $santa->draw->id]);
        $this->targetName = $santa->target->name;
        $this->content = $content;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.dearsanta')
                    ->text('emails.dearsanta_plain');
    }
}
