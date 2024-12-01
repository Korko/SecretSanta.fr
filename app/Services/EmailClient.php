<?php

namespace App\Services;

use Webklex\IMAP\Facades\Client;
use Webklex\PHPIMAP\Message;

class EmailClient
{
    protected $delegate;

    public function __construct($connexion = 'default')
    {
        $this->delegate = Client::account($connexion);
        $this->delegate->connect();
    }

    public function __destruct()
    {
        $this->delegate->expunge();
    }

    public function getUnseenMails(int $limit = 0): Iterable
    {
        $oFolder = $this->delegate->getFolder(config('imap.folders.inbox'));

        return $oFolder->query()
            ->whereUnseen()
            ->leaveUnread()
            ->limit($limit)
            ->get();
    }

    public function delete(Message $message): void
    {
        $message->delete(false);
    }

    /**
     * Proxies all methods to the delegate.
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    public function __call($method, $args)
    {
        return call_user_func_array([$this->delegate, $method], $args);
    }
}
