<?php

namespace App\Services;

use Webklex\IMAP\Facades\Client;
use Webklex\PHPIMAP\Message;
use Webklex\PHPIMAP\Query\WhereQuery;

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

    protected function getFolderQuery($folder)
    {
        $oFolder = $this->delegate->getFolder($folder);

        return $oFolder->query();
    }

    protected function getFolderLimitQuery($folder, int $limit = 0)
    {
        $query = $this->getFolderQuery($folder);

        if ($limit > 0) {
            $query->limit($limit);
        } else {
            $query->all();
        }

        return $query;
    }

    public function getMails($folder, int $limit = 0): iterable
    {
        return $this->getFolderLimitQuery($folder, $limit)
            ->get();
    }

    public function getUnseenMails(int $limit = 0, $folder = 'imap.folders.inbox'): iterable
    {
        return $this->getFolderLimitQuery(config($folder), $limit)
            ->whereUnseen()
            ->leaveUnread()
            ->get();
    }

    public function getOldMails(int $age = 30, int $limit = 0, $folder = 'imap.folders.trash'): iterable
    {
        return $this->getFolderLimitQuery(config($folder), $limit)
            ->whereBefore('-'.$age.' days')
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
