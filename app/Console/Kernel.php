<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule
            ->command('queue:work --stop-when-empty')
            ->everyMinute()
            ->withoutOverlapping();

        $schedule
            ->command('model:prune')
            ->daily()
            ->environments(['prod']);

        $schedule
            ->command('secretsanta:parse-bounces')
            ->everyMinute()
            ->withoutOverlapping()
            ->environments(['prod']);

        $schedule
            ->command('secretsanta:clean-bounces')
            ->daily()
            ->environments(['prod']);
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
