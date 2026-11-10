<?php

declare(strict_types=1);

use App\Providers\ComposerServiceProvider;
use Illuminate\Contracts\Queue\ShouldQueueAfterCommit;

arch()->preset()->php();
arch()->preset()->security()->ignoring(['assert', ComposerServiceProvider::class]); // fixed git commands for the footer version

// kit presets not enabled: strict() forbids protected methods in final classes (Eloquent accessors),
// laravel() needs resource-style controllers.

// Sent from inside transactions (new draw, participants change): nothing must go out if it rolls back
arch('notifications are queued after the transaction commits')
    ->expect('App\Notifications')
    ->classes()
    ->toImplement(ShouldQueueAfterCommit::class);
