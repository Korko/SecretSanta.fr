<?php

namespace App\Mail;

use Illuminate\Mail\Mailable as BaseMailable;

class Mailable extends BaseMailable
{
    /**
     * Send the mail and call the success method if exists
     *
     * @param  \Illuminate\Contracts\Mail\Factory|\Illuminate\Contracts\Mail\Mailer  $mailer
     * @return void
     */
    public function send($mailer)
    {
        parent::send($mailer);

        if (method_exists($this, 'success')) {
            $this->success();
        }
    }
}
