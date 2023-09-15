<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Events\MigrationsEnded;
use Illuminate\Database\Events\MigrationsStarted;
use Illuminate\Events\Dispatcher;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Schema;

class MigrationSubscriber
{
    /**
     * Handle migration batch started events.
     */
    public function handleMigrationsStarted(): void {
        Schema::disableForeignKeyConstraints();
    }

    /**
     * Handle migration batch ended events.
     */
    public function handleMigrationsEnded(): void {
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Register the listeners for the subscriber.
     */
    public function subscribe(): array
    {
        return [
            MigrationsStarted::class => 'handleMigrationsStarted',
            MigrationsEnded::class => 'handleMigrationsEnded',
        ];
    }
}
