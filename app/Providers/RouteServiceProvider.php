<?php

namespace App\Providers;

use App\Facades\DrawCrypt;
use App\Models\PendingDraw;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Define your route model bindings, pattern filters, etc.
     */
    public function boot(): void
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
            return $this->route($name, $parameters, $expiration, $absolute).'#'.base64_encode(DrawCrypt::getIV());
        });

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
            return $this->signedRoute($name, $parameters, $expiration, $absolute).'#'.base64_encode(DrawCrypt::getIV());
        });

        RateLimiter::for('global', function (Request $request) {
            return Limit::perMinute(100)->by($request->ip())->response(function () {
                return abort(429);
            });
        });

        Route::pattern('draw', '[0-9a-zA-Z]{'.config('hashids.connections.draw.length').',}');
        Route::pattern('pendingDraw', '[0-9a-zA-Z]{'.config('hashids.connections.pendingDraw.length').',}');
        Route::pattern('pendingParticipant', '[0-9a-zA-Z]{'.config('hashids.connections.pendingParticipant.length').',}');
        Route::pattern('participant', '[0-9a-zA-Z]{'.config('hashids.connections.participant.length').',}');
        Route::pattern('dearSanta', '[0-9a-zA-Z]{'.config('hashids.connections.dearSanta.length').',}');
        Route::pattern('dearTarget', '[0-9a-zA-Z]{'.config('hashids.connections.dearTarget.length').',}');

        Route::bind('user', function (string $value) {
            return PendingDraw::where('name', $value)->firstOrFail();
        });

        $this->routes(function () {
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}
