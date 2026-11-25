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

// The lock must expire on its own if the worker gets killed (OOM, kill -9...):
// Laravel's default is 24 hours, during which no email would be sent.
// --max-time keeps a running worker under that delay.
Schedule::command('queue:work --stop-when-empty --max-time=540')
    ->everyMinute()
    ->withoutOverlapping(10);

Schedule::command('model:prune')
    ->daily()
    ->environments(['prod']);

Schedule::command('secretsanta:parse-bounces')
    ->everyMinute()
    ->withoutOverlapping(10)
    ->environments(['prod']);

Schedule::command('secretsanta:clean-bounces')
    ->daily()
    ->environments(['prod']);
