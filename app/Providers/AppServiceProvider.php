<?php

namespace App\Providers;

use App\Facades\DrawCrypt;
use App\Services\CsvGenerator;
use App\Services\Response;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Support\ServiceProvider;
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
            \App\Solvers\HatSolver::class
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
    }
}
