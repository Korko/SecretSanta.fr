<?php

namespace App\Providers;

use Illuminate\Encryption\EncryptionServiceProvider as ServiceProvider;
use Korko\Encrypter\SymmetricalEncrypter;

class EncryptionServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('encrypter', function ($app) {
            return new SymmetricalEncrypter(SymmetricalEncrypter::generateKey('AES-256-CBC'), 'AES-256-CBC');
        });
    }
}
