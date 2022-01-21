<?php

use App\Models\Draw;
use App\Models\PendingDraw;
use App\Models\Participant;
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

    $draw = PendingDraw::find(1);

    Notification::assertSentTo(
        new AnonymousNotifiable,
        PendingDrawNotification::class,
        function ($notification, $channels, $notifiable) use ($draw) {
            return $notifiable->routes['mail'] === [['name' => $draw->organizer_name, 'email' => $draw->organizer_email]];
        }
    );
})->with('participants list');;