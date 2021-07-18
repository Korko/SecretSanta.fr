<?php

namespace App\Providers;

use App\Services\IVEncrypter;
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
        $this->app->singleton('draw-encrypter', function ($app) {
            $config = $app->make('config')->get('app');

            return new IVEncrypter($this->parseKey($config), $config['cipher']);
        });
    }
}
