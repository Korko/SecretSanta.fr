<?php

namespace App\Providers;

use App\Services\SymmetricalEncrypter;
use Illuminate\Encryption\EncryptionServiceProvider as ServiceProvider;

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
            return new SymmetricalEncrypter(SymmetricalEncrypter::generateKey('AES-256-CBC'));
        });
    }
}
