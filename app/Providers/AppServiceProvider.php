<?php

namespace App\Providers;

use App\Services\IVEncrypter;
use Closure;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\ResponseFactory;
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
        Blueprint::macro('tinyBlob', function ($column) {
            /** @var Blueprint $this */
            return $this->addColumn('blob', $column, ['length' => IVEncrypter::TINYBLOB_MAXLENGTH])->charset('')->collation('');
        });
        Blueprint::macro('blob', function ($column) {
            /** @var Blueprint $this */
            return $this->addColumn('blob', $column, ['length' => IVEncrypter::BLOB_MAXLENGTH])->charset('')->collation('');
        });

        /**
         * @see JsonResponse::setData
         */
        JsonResponse::macro('addData', function ($data): JsonResponse {
            /** @var JsonResponse $this */
            $originalData = $this->getData(true);
            $this->setData($data);
            $newData = $this->getData(true);

            return $this->setData($originalData + $newData);
        });

        ResponseFactory::macro('jsonTry', function (Closure $closure, $onSuccess, $onFailure): JsonResponse {
            /** @var ResponseFactory $this */
            try {
                $data = $closure() ?: [];

                return $this
                    ->json([
                        'message' => $onSuccess,
                    ])
                    ->addData($data);
            } catch(Exception) {
                return $this
                    ->json([
                        'message' => $onFailure,
                    ], 401);
            }
        });

        $this->bootInertia();

        Model::shouldBeStrict(! $this->app->isProduction());
    }

    /**
     * Initialize inertia js
     */
    private function bootInertia(): void
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
                'translations' => fn () => translations(),
            ],
        ]);

        ResponseFactory::macro('inertia', function (string $page, array $parameters = []) {
            return Inertia::render($page, $parameters)
                ->withViewData([
                    'version' => exec('git describe --tags --always --abbrev=0'),
                ]);
        });
    }
}
