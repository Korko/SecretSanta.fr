<?php

namespace App\Console;

use App\DearSantaDraw;
use App\MailBody;
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
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            DearSantaDraw::where('expiration', '<=', DB::raw('CURRENT_TIMESTAMP'))->delete();
            MailBody::where('created_at', '<=', DB::raw('DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 1 DAY)'))->delete();
        })->daily();
    }
}
