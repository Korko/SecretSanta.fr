<?php

namespace App\Services;

use Webklex\IMAP\Facades\Client;

class EmailClient
{
	protected $delegate;

	public function __construct($connexion = 'default')
	{
        $this->delegate = Client::account($connexion);
        $this->delegate->connect();
	}

    public function getUnseenMails(): Iterable
    {
        $oFolder = $this->delegate->getFolder(config('imap.folders.inbox'));

        return $oFolder->query()->whereUnseen()->get();
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