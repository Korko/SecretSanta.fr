<?php

namespace App\Mail;

use Illuminate\Container\Container;
use Illuminate\Mail\Mailable as BaseMailable;

class Mailable extends BaseMailable
{
    public function send($mailer)
    {
        parent::send($mailer);

        if (method_exists($this, 'success')) {
            $this->success();
        }
    }
}
