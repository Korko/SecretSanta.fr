<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Schedule::command('queue:work --stop-when-empty')
    ->everyMinute()
    ->withoutOverlapping();

Schedule::command('model:prune')
    ->daily()
    ->environments(['prod']);

Schedule::command('secretsanta:parse-bounces')
    ->everyMinute()
    ->withoutOverlapping()
    ->environments(['prod']);

Schedule::command('secretsanta:clean-bounces')
    ->daily()
    ->environments(['prod']);

// A failed job keeps the draw secret in clear in its payload (data.iv, next to the encrypted command): a week
// leaves time to retry after a mail outage (secretsanta retry), then it goes, the organizer can resend from the panel
Schedule::command('queue:prune-failed --hours=168')
    ->daily()
    ->environments(['prod']);
