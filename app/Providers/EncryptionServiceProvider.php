<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\IVEncrypter;
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
         * and the draw encrypter (using a custom generated key)
         */
        $this->app->singleton('draw-encrypter', fn (): IVEncrypter => new IVEncrypter($this->parseKey(config()->array('app')), config()->string('app.cipher'))
            ->previousKeys(array_map(fn (mixed $key): string => $this->parseKey(['key' => $key]), config()->array('app.previous_keys'))));
    }
}
