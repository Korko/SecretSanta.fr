<?php

namespace App\Mail;

use App\Models\DearSanta as DearSantaEntry;
use Illuminate\Bus\Queueable;

class DearSanta extends TrackedMailable
{
    use Queueable;

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
