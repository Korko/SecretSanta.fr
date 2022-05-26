<?php

use App\Enums\AppMode;
use App\Models\Draw;
use App\Models\Participant;
use App\Models\PendingDraw;
use App\Notifications\PendingDraw as PendingDrawNotification;
use Illuminate\Notifications\AnonymousNotifiable;

it('can create pending draws (and just pending ones)', function ($participants) {
    Notification::fake();

    assertModelCount(PendingDraw::class, 0);

    createDraw($participants)
        ->assertSuccessful();

    assertModelCount(PendingDraw::class, 1);

    assertModelCount(Draw::class, 0);
    assertModelCount(Participant::class, 0);

    $draw = PendingDraw::first();

    Notification::assertSentTo(
        new AnonymousNotifiable,
        PendingDrawNotification::class,
        function ($notification, $channels, $notifiable) use ($draw) {
            return $notifiable->routes['mail'] === [['name' => $draw->organizer_name, 'email' => $draw->organizer_email]];
        }
    );
})->with('participants list');

it('respects limit in participants count', function ($participants) {
    Notification::fake();

    config()->set('modes.limitations.participants.'.(AppMode::FREE)->value, count($participants) - 1);

    createDraw($participants)
        ->assertUnprocessable()
        ->assertJsonValidationErrors([
            'participants'
        ]);

    config()->set('modes.limitations.participants.'.(AppMode::FREE)->value, count($participants));

    createDraw($participants)
        ->assertSuccessful();
})->with('participants list');