<?php

namespace App\Mailbox;

use App\Contracts\Mailbox;
use Illuminate\Contracts\Foundation\Application;
use InvalidArgumentException;
use Webklex\PHPIMAP\ClientManager as ImapClientManager;

class MailboxManager
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * The array of resolved mailboxes.
     *
     * @var array
     */
    protected $mailboxes = [];

    /**
     * Create a new Mailbox manager instance.
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Get a mailbox instance by name.
     */
    public function mailbox(?string $name = null): Mailbox
    {
        $name = $name ?: $this->getDefaultDriver();

        return $this->mailboxes[$name] = $this->get($name);
    }

    /**
     * Attempt to get the mailer from the local cache.
     */
    protected function get(string $name): Mailbox
    {
        return $this->mailers[$name] ?? $this->resolve($name);
    }

    /**
     * Resolve the given mailer.
     *
     * @throws \InvalidArgumentException
     */
    protected function resolve(string $name): Mailbox
    {
        $config = $this->getConfig($name);

        if (is_null($config)) {
            throw new InvalidArgumentException("Mailbox [{$name}] is not defined.");
        }

        return $this->createTransport($config);
    }

    /**
     * Get the mailbox connection configuration.
     */
    protected function getConfig(string $name): array
    {
        return $this->app['config']["mailbox.mailboxes.{$name}"];
    }

    /**
     * Get the default mailbox driver name.
     */
    public function getDefaultDriver(): string
    {
        return $this->app['config']['mailbox.default'];
    }

    protected function createTransport($config)
    {
        $transport = $config['transport'];

        if (trim($transport ?? '') === '' || ! method_exists($this, $method = 'create'.ucfirst($transport).'Transport')) {
            throw new InvalidArgumentException("Unsupported mailbox transport [{$transport}].");
        }

        return $this->{$method}($config);
    }

    /**
     * Create an instance of the Webklex IMAP driver.
     */
    protected function createImapTransport(array $config): ImapTransport
    {
        $manager = $this->app->make(ImapClientManager::class);

        $account = $manager->account(
            $config['account'] ?? $this->app['config']->get('imap.default')
        );

        $account->connect();

        return $account;
    }

    /**
     * Create an instance of the Array Transport Driver.
     */
    protected function createArrayTransport(): ArrayTransport
    {
        return new ArrayTransport;
    }
}
