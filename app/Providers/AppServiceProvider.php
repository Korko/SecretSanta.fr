<?php

namespace App\Providers;

use App\Mail\DkimTransport;
use App\Solvers\HatSolver;
use App\Solvers\SolverInterface;
use DrawCrypt;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mime\Crypto\DkimSigner;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            SolverInterface::class,
            HatSolver::class
        );

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->bootJobs();
        $this->bootRoute();
        $this->bootMail();
    }

    public function bootMail(): void
    {
        Mail::extend('dkim', function (array $config) {
            $signer = new DkimSigner(
                'file://'.config('mail.dkim_private_key'),
                config('mail.dkim_domain'),
                config('mail.dkim_selector'),
                ['algorithm' => config('mail.dkim_algo')],
                config('mail.dkim_passphrase') ?? '',
            );

            return new DkimTransport(
                Mail::createSymfonyTransport(config('mail.mailers.'.$config['mailer'])),
                $signer,
            );
        });
    }

    public function bootJobs(): void
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

    public function bootRoute(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
