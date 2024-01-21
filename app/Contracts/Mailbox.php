<?php

namespace App\Contracts;

interface Mailbox
{
    /**
     * @return \iterable<\App\Contracts\EmailMessage>
     */
    public function getUnseenMails(): iterable;
}
