<?php

namespace App\Mail;

use App\Models\Mail;
use Illuminate\Mail\Mailable;

class TrackedMailable extends Mailable
{
    protected $mail;

    /**
     * Create a new tracked mailable instance.
     *
     * @param  \App\Models\Mail  $mailer
     * @return void
     */
    public function __construct(Mail $mail)
    {
        $this->mail = $mail;
        $this->mail->markAsCreated();
    }

    /**
     * Get the mail instance.
     *
     * @return \App\Models\Mail
     */
    public function getMail()
    {
        return $this->mail;
    }
}
