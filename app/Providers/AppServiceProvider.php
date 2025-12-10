<?php

namespace App\Providers;

use App\Channels\MailChannel;
use App\Solvers\HatSolver;
use App\Solvers\SolverInterface;
use DrawCrypt;
use Illuminate\Foundation\Application;
use Illuminate\Mail\Markdown;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Queue\Events\JobProcessing;
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
            SolverInterface::class,
            HatSolver::class
        );

        $channelManager = $this->app->get(ChannelManager::class);
        $channelManager->extend('mail', function (Application $application) {
            return new MailChannel($application->get('mail.manager'), $application->get(Markdown::class));
        });
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
                    'iv' => base64_encode(DrawCrypt::getIV()),
                ]),
            ];
        });

        $this->app['events']->listen(JobProcessing::class, function ($event) {
            if (isset($event->job->payload()['data']['iv'])) {
                DrawCrypt::setIV(base64_decode($event->job->payload()['data']['iv']));
            }
        });
    }
}
