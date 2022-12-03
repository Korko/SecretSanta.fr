<?php

use App\Notifications\OrganizerRecap;
use App\Notifications\TargetDrawn;
use Illuminate\Notifications\AnonymousNotifiable;

it('sends no notifications in case of error', function ($participants) {
    Notification::fake();

    $pendingDraw = createPendingDraw($participants);

    ajaxGet(URL::signedRoute('pending.process', ['pending' => $pendingDraw->id]))
        ->assertSuccessful();

    Notification::assertNothingSent();
})->with('invalid participants list');

it('sends notifications in case of success', function ($participants) {
    Notification::fake();

    $pendingDraw = createPendingDraw($participants);

    ajaxGet(URL::signedRoute('pending.process', ['pending' => $pendingDraw->id]))
        ->assertSuccessful();

    $draw = $pendingDraw->fresh()->draw;
    assertModelExists($draw);

    // Ensure Organizer receives his recap
    Notification::assertSentTimes(OrganizerRecap::class, 1);
    Notification::assertSentTo($draw->organizer, OrganizerRecap::class);

    // Ensure Participants receive their own recap
    Notification::assertSentTimes(TargetDrawn::class, count($draw->participants));
    foreach ($draw->participants as $participant) {
        Notification::assertSentTo($participant, TargetDrawn::class);
    }
})->with('participants list');

it('can create draws with a non participant organizer', function ($participants) {
    Notification::fake();

    $pendingDraw = createPendingDraw($participants, [
        'participant-organizer' => '0',
        'organizer' => ['name' => 'foo', 'email' => 'foo@foobar.com'],
    ]);

    ajaxGet(URL::signedRoute('pending.process', ['pending' => $pendingDraw->id]))
        ->assertSuccessful();

    $draw = $pendingDraw->fresh()->draw;

    assertEquals('foo', $draw->organizer_name);
    assertEquals('foo@foobar.com', $draw->organizer_email);

    // Ensure Organizer receives his recap
    Notification::assertSentTimes(OrganizerRecap::class, 1);
    Notification::assertSentTo($draw->organizer, OrganizerRecap::class);

    // Ensure Participants receive their own recap
    Notification::assertSentTimes(TargetDrawn::class, count($draw->participants));
    foreach ($draw->participants as $participant) {
        assertNotEquals($participant->email, $draw->organizer_email);
        Notification::assertSentTo($participant, TargetDrawn::class);
    }
})->with('participants list');

it('sends to the organizer the link to their panel', function ($participants) {
    Notification::fake();

    $pendingDraw = createPendingDraw($participants);

    ajaxGet(URL::signedRoute('pending.process', ['pending' => $pendingDraw->id]))
        ->assertSuccessful();

    $draw = $pendingDraw->fresh()->draw;

    // Ensure Organizer receives his recap
    Notification::assertSentTo($draw->organizer, OrganizerRecap::class, function ($notification, $channels, $notifiable) use ($draw) {
        return
            $notification->toMail($notifiable)->assertSeeInHtml(
                URL::hashedSignedRoute('organizer.index', ['draw' => $draw->hash])
            );
    });
})->with('participants list');

it('sends to the organizer their initial recap by mail', function ($participants) {
    Notification::fake();

    $pendingDraw = createPendingDraw($participants);

    ajaxGet(URL::signedRoute('pending.process', ['pending' => $pendingDraw->id]))
        ->assertSuccessful();

    $draw = $pendingDraw->fresh()->draw;

    // Ensure Organizer receives his recap
    Notification::assertSentTo($draw->organizer, OrganizerRecap::class, function ($notification, $channels, $notifiable) use ($draw) {
        $attachments = $notification->toMail($notifiable)->build()->rawAttachments;

        assertCount(1, $attachments);
        assertEquals('text/csv', $attachments[0]['options']['mime']);

        // CSV have a BOM at start, remove it to parse, then check the amount of lines not starting with '#' (comments in CSV)
        $attachments[0]['data'] = str_replace("\xEF\xBB\xBF", '', $attachments[0]['data']);
        assertCount($draw->participants()->count(), collect(explode("\n", $attachments[0]['data']))
            ->map(fn($line) => str_getcsv($line))
            ->filter(fn($data) => $data[0][0] !== '#')
        );

        return TRUE;
    });
})->with('participants list');

it('can deal with thousands of participants', function ($participants) {
    Notification::fake();

    $pendingDraw = createPendingDraw($participants);

    ajaxGet(URL::signedRoute('pending.process', ['pending' => $pendingDraw->id]))
        ->assertSuccessful();

    $draw = $pendingDraw->fresh()->draw;

    assertEquals(count($participants), $draw->participants()->count());
})->with('huge participants list')->group('massive');
