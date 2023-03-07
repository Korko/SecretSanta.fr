<?php

namespace App\Providers;

use App\Contracts\Mailbox;
use App\Mailbox\MailboxManager;
use App\Services\SigningMailer;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class MailServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->replaceMailer();
        $this->registerMailbox();
    }

    /**
     * Replace the built-in mailer to allow for DKIM signed mails
     */
    protected function replaceMailer(): void
    {
        $this->app->bind('mailer', function () {
            return $this->app->make(SigningMailer::class);
        });
    }

    /**
     * Register the mailbox instance.
     */
    protected function registerMailbox(): void
    {
        $this->app->singleton('mailbox.manager', function ($app) {
            return new MailboxManager($app);
        });

        $this->app->bind(Mailbox::class, function ($app) {
            return $app->make('mailbox.manager')->mailbox();
        });
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [
            'mailbox.manager',
            Mailbox::class,
        ];
    }
}
