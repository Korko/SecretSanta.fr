<?php

namespace App\Contracts;

interface Mailbox
{
    public function getUnseenMails() : Iterable;
}