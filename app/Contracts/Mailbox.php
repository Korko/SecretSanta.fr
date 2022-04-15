<?php

namespace App\Contracts;

interface Mailbox
{
    /**
     * @return \Iterable<\App\Contracts\EmailMessage>
     */
    public function getUnseenMails() : Iterable;
}