<?php

namespace App\Mailbox;

use App\Contracts\Mailbox as MailboxContract;
use Illuminate\Support\Collection;
use Webklex\IMAP\Facades\Client as WebklexClient;

class ImapTransport implements MailboxContract
{
    public function __construct(
        protected readonly WebklexClient $delegate
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getUnseenMails(): iterable
    {
        $oFolder = $this->delegate->getFolder(config('imap.folders.inbox'));

        return (new Collection($oFolder->query()->whereUnseen()->get()))
            ->map(function ($message) {
                return new ImapMessage($message);
            });
    }

    /**
     * Proxies all methods to the delegate.
     *
     * @return mixed
     */
    public function __call(string $method, array $args)
    {
        return call_user_func_array([$this->delegate, $method], $args);
    }
}
