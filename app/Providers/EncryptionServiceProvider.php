<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\DrawEncrypter;
use Illuminate\Encryption\EncryptionServiceProvider as ServiceProvider;

final class EncryptionServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register(): void
    {
        /**
         * Register another singleton to differenciate app encrypter (using the app_key)
         * and the draw encrypter (key derived from the app_key and the secret of the draw links)
         */
        $this->app->singleton('draw-encrypter', fn (): DrawEncrypter => new DrawEncrypter(
            $this->parseKey(config()->array('app')),
            config()->string('app.cipher'),
            array_values(array_map(fn (mixed $key): string => $this->parseKey(['key' => $key]), config()->array('app.previous_keys'))),
        ));
    }
}
