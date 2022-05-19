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
     *
     * @return void
     */
    public function register()
    {
        $this->replaceMailer();
        $this->registerMailbox();
    }

    /**
     * Replace the built-in mailer to allow for DKIM signed mails
     *
     * @return void
     */
    protected function replaceMailer()
    {
        $this->app->bind('mailer', function() {
            return $this->app->make(SigningMailer::class);
        });
    }

    /**
     * Register the mailbox instance.
     *
     * @return void
     */
    protected function registerMailbox()
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
     *
     * @return array
     */
    public function provides()
    {
        return [
            'mailbox.manager',
            Mailbox::class
        ];
    }
}
