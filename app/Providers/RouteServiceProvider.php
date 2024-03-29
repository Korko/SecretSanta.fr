<?php

namespace App\Providers;

use App\Enums\DrawStatus;
use App\Facades\DrawCrypt;
use App\Models\Draw;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Define your route model bindings, pattern filters, etc.
     */
    public function boot(): void
    {
        $this->addUrlMacros();
        $this->addRouteBindings();

        $this->routes(function () {
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    protected function addUrlMacros(): void
    {
        /**
         * Create a route URL for a named route appended with the IV.
         *
         * @param  string  $name
         * @param  mixed  $parameters
         * @param  \DateTimeInterface|\DateInterval|int|null  $expiration
         * @param  bool  $absolute
         * @return string
         *
         * @throws \InvalidArgumentException
         */
        URL::macro('hashedRoute', function ($name, $parameters = [], $expiration = null, $absolute = true) {
            return $this->signedRoute($name, $parameters, $expiration, $absolute).'#'.base64_encode(DrawCrypt::getIV());
        });
    }

    protected function addRouteBindings(): void
    {
        RateLimiter::for('global', function (Request $request) {
            return Limit::perMinute(100)->by($request->ip())->response(function () {
                return abort(429);
            });
        });
    }
}
