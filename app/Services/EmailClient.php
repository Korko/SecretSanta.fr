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

    public function getUnseenMails(int $limit = 0, $folder = 'imap.folders.inbox'): iterable
    {
        $oFolder = $this->delegate->getFolder(config($folder));

        return $oFolder->query()
            ->whereUnseen()
            ->leaveUnread()
            ->limit($limit)
            ->get();
    }

    public function getOldMails(int $age = 30, int $limit = 0, $folder = 'imap.folders.trash'): iterable
    {
        $oFolder = $this->delegate->getFolder(config($folder));

        return $oFolder->query()
            ->whereBefore('-'.$age.' days')
            ->limit($limit)
            ->get();
    }

    public function trash(Message $message): void
    {
        $message->move(config('imap.folders.trash'));
    }

    public function delete(Message $message): void
    {
        $message->delete(false);
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
