<?php

namespace App\Mail;

use App\Models\DearSanta as DearSantaEntry;

class DearSanta extends TrackedMailable
{
    protected $dearSanta;

    public $targetName;
    public $content;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(DearSantaEntry $dearSanta)
    {
        $this->dearSanta = $dearSanta;

        $this->subject = __('emails.dear_santa.title', ['draw' => $dearSanta->sender->draw->id]);

        $this->content = $dearSanta->mail_body;

        $this->targetName = $dearSanta->sender->name;

        $this->track($dearSanta->mail);
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
