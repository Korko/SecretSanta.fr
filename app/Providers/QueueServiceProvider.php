<?php

namespace App\Providers;

use App\Facades\DrawCrypt;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Support\ServiceProvider;
use Queue;

class QueueServiceProvider extends ServiceProvider
{
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