<?php

use App\Models\DearSanta;
use App\Models\DearTarget;
use App\Models\Draw;
use App\Services\DrawHandler;
use Illuminate\Support\Facades\Notification;

// Should use yield with a Closure to call the actual closure when the test runs
// So after the setupBeforeClass, else we don't have a database up and cannot insert anything

dataset('basic draw', function () {
    yield 'basic draw #0' => function() {
        Notification::fake();

        $draw = Draw::factory()
            ->hasParticipants(3)
            ->create();

        DrawHandler::solve($draw, $draw->participants);

        return $draw;
    };
});

dataset('large draw', function () {
    yield 'large draw #0' => function() {
        Notification::fake();

        $draw = Draw::factory()
            ->hasParticipants(10)
            ->create();

        DrawHandler::solve($draw, $draw->participants);

        return $draw;
    };
});

dataset('finished draw', function () {
    yield 'finished draw #0' => function() {
        Notification::fake();

        $draw = Draw::factory()
            ->isFinished()
            ->hasParticipants(3)
            ->create();

        DrawHandler::solve($draw, $draw->participants);

        return $draw;
    };
});

dataset('expired draw', function () {
    yield 'expired draw #0' => function() {
        Notification::fake();

        $draw = Draw::factory()
            ->isExpired()
            ->hasParticipants(3)
            ->create();

        DrawHandler::solve($draw, $draw->participants);

        return $draw;
    };
});

dataset('dear santa', function () {
    yield 'dear santa #0' => function() {
        Notification::fake();

        $draw = Draw::factory()
            ->hasParticipants(3)
            ->create();

        DrawHandler::solve($draw, $draw->participants);

        return DearSanta::factory()
            ->for($draw->participants->random(), 'sender')
            ->create();
    };
});

dataset('resendable dear santa', function () {
    yield 'resendable dear santa #0' => function() {
        Notification::fake();

        $draw = Draw::factory()
            ->hasParticipants(3)
            ->create();

        DrawHandler::solve($draw, $draw->participants);

        return DearSanta::factory()
            ->resendable()
            ->for($draw->participants->random(), 'sender')
            ->create();
    };
});

dataset('dear target', function () {
    yield 'dear target #0' => function() {
        Notification::fake();

        $draw = Draw::factory()
            ->hasParticipants(3)
            ->create();

        DrawHandler::solve($draw, $draw->participants);

        return DearTarget::factory()
            ->for($draw->participants->random(), 'sender')
            ->create();
    };
});

dataset('resendable dear target', function () {
    yield 'resendable dear target #0' => function() {
        Notification::fake();

        $draw = Draw::factory()
            ->hasParticipants(3)
            ->create();

        DrawHandler::solve($draw, $draw->participants);

        return DearTarget::factory()
            ->resendable()
            ->for($draw->participants->random(), 'sender')
            ->create();
    };
});
