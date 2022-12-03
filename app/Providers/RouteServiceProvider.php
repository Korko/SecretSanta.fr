<?php

namespace App\Providers;

use App\Facades\DrawCrypt;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        /**
         * Create a signed route URL for a named route appended with the IV.
         *
         * @param  string  $name
         * @param  mixed  $parameters
         * @param  \DateTimeInterface|\DateInterval|int|null  $expiration
         * @param  bool  $absolute
         * @return string
         *
         * @throws \InvalidArgumentException
         */
        URL::macro('hashedSignedRoute', function ($name, $parameters = [], $expiration = null, $absolute = true) {
            return $this->signedRoute($name, $parameters, $expiration, $absolute) . '#' . base64_encode(DrawCrypt::getIV());
        });

        $this->routes(function () {
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}
