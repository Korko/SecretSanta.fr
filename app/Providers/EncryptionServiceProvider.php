<?php

namespace App\Providers;

use App\Services\Encrypter;
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
        /**
         * Register another singleton to differenciate app encrypter (using the app_key)
         * and the draw encrypter (using a custom generated key)
         */
        $this->app->singleton('draw-encrypter', function () {
            return new Encrypter(Encrypter::generateKey('AES-256-CBC'), 'AES-256-CBC');
        });
    }
}
