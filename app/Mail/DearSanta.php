<?php

namespace App\Mail;

use App\DearSanta as DearSantaEntry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;

class DearSanta extends Mailable
{
    use Queueable, UpdatesDeliveryStatus;

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

        $this->content = $dearSanta->email_body;

        $this->targetName = $dearSanta->sender->name;

        $this->trackEntry($dearSanta);
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
