<?php

namespace App\Mail;

use App\DearSanta as DearSantaEntry;
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
    public function __construct(DearSantaEntry $dearSanta)
    {
        $this->subject = __('emails.dear_santa.title', ['draw' => $dearSanta->sender->draw->id]);
        $this->targetName = $dearSanta->sender->name;
        $this->content = $dearSanta->email_body;
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
