<?php

declare(strict_types=1);

use App\Providers\ComposerServiceProvider;
use Illuminate\Contracts\Queue\ShouldQueueAfterCommit;
use Illuminate\Queue\Attributes\Backoff;
use Illuminate\Queue\Attributes\Tries;

arch()->preset()->php();
arch()->preset()->security()->ignoring(['assert', ComposerServiceProvider::class]); // fixed git commands for the footer version

// kit presets not enabled: strict() forbids protected methods in final classes (Eloquent accessors),
// laravel() needs resource-style controllers.

// Sent from inside transactions (new draw, participants change): nothing must go out if it rolls back
arch('notifications are queued after the transaction commits')
    ->expect('App\Notifications')
    ->classes()
    ->toImplement(ShouldQueueAfterCommit::class);

// A failed job is not kept (config/queue.php): a mail server hiccup must not lose the email
arch('notifications are retried', function (): void {
    expect('App\Notifications')->classes()->toHaveAttribute(Tries::class);
    expect('App\Notifications')->classes()->toHaveAttribute(Backoff::class);
});
