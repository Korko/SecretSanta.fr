<?php

namespace App\Providers;

use App\Services\IVEncrypter;
use Closure;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Solvers\SolverInterface::class,
            \App\Solvers\GraphSolver::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // TODO: This does not work anymore. Fix this or revert to using pure blob columns.
        Blueprint::macro('tinyBlob', function ($column) {
            /** @var Blueprint $this */
            return $this->addColumn('binary', $column, ['length' => IVEncrypter::TINYBLOB_MAXLENGTH]);
        });
        Blueprint::macro('blob', function ($column) {
            /** @var Blueprint $this */
            return $this->addColumn('binary', $column, ['length' => IVEncrypter::BLOB_MAXLENGTH])   ;
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

        ResponseFactory::macro('jsonTry', function (Closure $closure, $onSuccess, $onFailure, $exceptionClass = \Exception::class, $errorCode = 500): JsonResponse {
            /** @var ResponseFactory $this */
            try {
                $data = $closure() ?: [];

                return $this
                    ->json([
                        'message' => $onSuccess,
                    ])
                    ->addData($data);
            } catch(Exception $e) {
                if (is_a($e, $exceptionClass)) {
                    return $this
                        ->json([
                            'message' => $onFailure,
                            'error' => config('app.debug') ? $e->getMessage() : null,
                        ], $errorCode);
                }

                throw $e;
            }
        });

        $this->bootInertia();

        View::share('version', exec('git describe --tags --always --abbrev=0'));

        //Model::shouldBeStrict(! $this->app->isProduction());
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
                ])
                ->toResponse(request());
        });
    }
}
