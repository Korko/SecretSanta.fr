<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
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
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
