<?php

namespace App\Providers;

use Illuminate\Http\Response;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

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

        Response::macro('inertia', function (string $page, array $parameters = []) {
            return Inertia::render($page, $parameters)
                ->withViewData([
                    'version' => exec('git describe --tags --always --abbrev=0')
                ]);
        });
    }
}
