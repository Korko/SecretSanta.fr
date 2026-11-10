<?php

declare(strict_types=1);

namespace App\Providers;

use App\Mail\DkimTransport;
use App\Solvers\HatSolver;
use App\Solvers\SolverInterface;
use DrawCrypt;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\Request;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\RateLimiter;
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

        // Dates are written in the app timezone: the session must use the same one so that MySQL stores real UTC.
        // The current offset (DST included), computed per request as the config may be cached
        foreach (['mysql', 'mariadb'] as $connection) {
            config(["database.connections.$connection.timezone" => date('P')]);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->bootJobs();
        $this->bootMail();
        $this->bootRateLimiters();
    }

    // Here and not in routes/web.php, which is not loaded once the routes are cached
    public function bootRateLimiters(): void
    {
        // Every page and call (whole web group, see bootstrap/app.php)
        RateLimiter::for('global', fn (Request $request): Limit => Limit::perMinute(100)->by((string) $request->ip()));

        // Each of these sends emails, to any address for a new draw: keeps the domain reputation safe from spam
        RateLimiter::for('draw', fn (Request $request): Limit => Limit::perHour(20)->by((string) $request->ip()));
        RateLimiter::for('email', fn (Request $request): Limit => Limit::perHour(60)->by((string) $request->ip()));
    }

    public function bootMail(): void
    {
        Mail::extend('dkim', function (array $config): DkimTransport {
            $signer = new DkimSigner(
                'file://'.config()->string('mail.dkim_private_key'),
                config()->string('mail.dkim_domain'),
                config()->string('mail.dkim_selector'),
                ['algorithm' => config('mail.dkim_algo')],
                config()->string('mail.dkim_passphrase'),
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
