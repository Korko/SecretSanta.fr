<?php

declare(strict_types=1);

namespace App\Providers;

use App\Mail\DkimTransport;
use App\Solvers\HatSolver;
use App\Solvers\SolverInterface;
use DrawCrypt;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Mime\Crypto\DkimSigner;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     */
    public const string HOME = '/home';

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
        $this->bootMail();
    }

    public function bootMail(): void
    {
        Mail::extend('dkim', function (array $config): DkimTransport {
            $signer = new DkimSigner(
                'file://'.config()->string('mail.dkim_private_key'),
                config()->string('mail.dkim_domain'),
                config()->string('mail.dkim_selector'),
                ['algorithm' => config('mail.dkim_algo')],
                config()->string('mail.dkim_passphrase', ''),
            );

            return new DkimTransport(
                Mail::createSymfonyTransport(config()->array('mail.mailers.'.(is_string($config['mailer'] ?? null) ? $config['mailer'] : ''))),
                $signer,
            );
        });
    }

    public function bootJobs(): void
    {
        Queue::createPayloadUsing(fn (?string $connection, ?string $queue, array $payload): array => [
            'data' => array_merge(is_array($payload['data'] ?? null) ? $payload['data'] : [], [
                'iv' => base64_encode(DrawCrypt::getIV()),
            ]),
        ]);

        $this->app->make(Dispatcher::class)->listen(JobProcessing::class, function (JobProcessing $event): void {
            $iv = data_get($event->job->payload(), 'data.iv');
            if (is_string($iv)) {
                DrawCrypt::setIV(base64_decode($iv));
            }
        });
    }
}
