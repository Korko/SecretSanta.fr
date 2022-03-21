<?php

namespace App\Providers;

use App\Facades\DrawCrypt;
use Illuminate\Foundation\Application;
use Illuminate\Mail\Markdown;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use Queue;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            \App\Solvers\SolverInterface::class,
            \App\Solvers\GraphSolver::class
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Queue::createPayloadUsing(function ($connection, $queue, $payload) {
            return [
                'data' => array_merge($payload['data'], [
                    'iv' => base64_encode(DrawCrypt::getIV())
                ])
            ];
        });

        $this->app['events']->listen(\Illuminate\Queue\Events\JobProcessing::class, function ($event) {
            if (isset($event->job->payload()['data']['iv'])) {
                DrawCrypt::setIV(base64_decode($event->job->payload()['data']['iv']));
            }
        });

        $this->bootInertia();
    }

    /**
     * Initialize inertia js
     */
    private function bootInertia() : void
    {
        // Boot inertia here. For example the version, the errors handlers...

        // Share the translations data in the props of the components.
        Inertia::share([
            'app' => [
                'name' => config('app.name'),
                'locale' => $this->app->getLocale(),

                // You can add a `locales => ['fr', 'en']` in your config.app
                // to represent you app supported locales.
                'locales' => config('app.locales'),

                // Here we properly return the translation to Vue.
                // Note that it is lazy loaded, so Inertia will not load the translations in every request.
                // Inertia will load only on demand. Using, VueJs, will call this method only once, when the app is open.
                'translations' => fn() => translations()
            ],
        ]);
    }
}
