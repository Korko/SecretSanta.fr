<?php

declare(strict_types=1);

namespace App\Services;

use RuntimeException;
use Webklex\IMAP\Facades\Client as ClientManager;
use Webklex\PHPIMAP\Client;
use Webklex\PHPIMAP\Message;
use Webklex\PHPIMAP\Query\WhereQuery;
use Webklex\PHPIMAP\Support\MessageCollection;

final readonly class EmailClient
{
    private Client $delegate;

    public function __construct(string $connexion = 'default')
    {
        $this->delegate = ClientManager::account($connexion);
        $this->delegate->connect();
    }

    public function __destruct()
    {
        $this->delegate->expunge();
    }

    /** @return MessageCollection<int, Message> */
    public function getMails(string $folder, int $limit = 0): MessageCollection
    {
        return $this->getFolderLimitQuery($folder, $limit)
            ->get();
    }

    /** @return MessageCollection<int, Message> */
    public function getUnseenMails(int $limit = 0): MessageCollection
    {
        return $this->getFolderLimitQuery(config()->string('imap.folders.inbox'), $limit)
            ->whereUnseen()
            ->leaveUnread()
            ->get();
    }

    /** @return MessageCollection<int, Message> */
    public function getOldMails(int $age = 30, int $limit = 0): MessageCollection
    {
        return $this->getFolderLimitQuery(config()->string('imap.folders.trash'), $limit)
            ->whereBefore('-'.$age.' days')
            ->get();
    }

    public function trash(Message $message): void
    {
        $message->move(config()->string('imap.folders.trash'));
    }

    public function delete(Message $message): void
    {
        $message->delete(false);
    }

    private function getFolderLimitQuery(string $folder, int $limit = 0): WhereQuery
    {
        $query = ($this->delegate->getFolder($folder) ?? throw new RuntimeException("Unknown IMAP folder {$folder}"))->query();

        return $limit > 0 ? $query->limit($limit) : $query->all();
    }
}
