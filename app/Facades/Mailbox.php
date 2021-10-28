<?php

namespace App\Facades;

use App\Contracts\Mailbox as MailboxContract;
use Illuminate\Support\Facades\Facade;

/**
 * @method static Iterable getUnseenMails()
 *
 * @see \App\Contracts\Mailbox
 */
class Mailbox extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return MailboxContract::class; }
}