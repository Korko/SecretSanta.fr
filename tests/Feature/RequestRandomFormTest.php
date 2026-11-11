<?php

declare(strict_types=1);

use App\Models\Draw;
use App\Models\Participant;
use App\Notifications\OrganizerRecap as OrganizerRecapNotif;
use App\Notifications\TargetDrawn;
use App\Services\URLParser;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;

use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotEquals;
use function PHPUnit\Framework\assertNull;

it('sends no notifications in case of error', function ($participants): void {
    Notification::fake();

    assertEquals(0, Draw::query()->count());
    assertEquals(0, Participant::query()->count());

    ajaxPost('/', [
        'participant-organizer' => '1',
        'participants' => $participants,
        'title' => 'this is a test',
        'content-email' => 'test mail {SANTA} => {TARGET}',
        'data-expiration' => date('Y-m-d', strtotime('+2 days')),
    ])
        ->assertStatus(422)
        ->assertJsonStructure(['message']);

    assertEquals(0, Draw::query()->count());
    assertEquals(0, Participant::query()->count());

    Notification::assertNothingSent();
})->with('invalid participants list');

it('can create draws', function (): void {
    assertEquals(0, Draw::query()->count());
    assertEquals(0, Participant::query()->count());

    $participants = generateParticipants(3);

    ajaxPost('/', [
        'participant-organizer' => '1',
        'participants' => $participants,
        'title' => 'this is a test',
        'content-email' => 'test mail {SANTA} => {TARGET}',
        'data-expiration' => date('Y-m-d', strtotime('+2 days')),
    ])
        ->assertSuccessful()
        ->assertJsonStructure(['message']);

    assertEquals(1, Draw::query()->count());
    assertEquals(3, Participant::query()->count());
});

it('sends notifications in case of success', function (): void {
    Notification::fake();

    ajaxPost('/', [
        'participant-organizer' => '1',
        'participants' => generateParticipants(3),
        'title' => 'this is a test',
        'content-email' => 'test mail {SANTA} => {TARGET}',
        'data-expiration' => date('Y-m-d', strtotime('+2 days')),
    ])
        ->assertSuccessful()
        ->assertJsonStructure(['message']);

    $draw = Draw::query()->find(1);

    // Taking part, the organizer gets the recap within their target email only
    Notification::assertNothingSentTo(new AnonymousNotifiable, OrganizerRecapNotif::class);
    Notification::assertSentTo($draw->participants->first(), TargetDrawn::class, function ($notification, $channels, $notifiable): bool {
        $mail = $notification->toMail($notifiable);

        return $mail->data()['organizer'] !== null && count($mail->rawAttachments) === 1;
    });
    Notification::assertSentTo($draw->participants->last(), TargetDrawn::class,
        fn ($notification, $channels, $notifiable): bool => $notification->toMail($notifiable)->data()['organizer'] === null && $notification->toMail($notifiable)->rawAttachments === []);

    // Ensure Participants receive their own recap
    Notification::assertSentTimes(TargetDrawn::class, count($draw->participants));
    foreach ($draw->participants as $participant) {
        Notification::assertSentTo($participant, TargetDrawn::class);
    }
});

it('can create draws with a non participant organizer', function (): void {
    Notification::fake();

    $participants = generateParticipants(3);

    ajaxPost('/', [
        'participant-organizer' => '0',
        'organizer' => ['name' => 'foo', 'email' => 'foo@foobar.com'],
        'participants' => $participants,
        'title' => 'this is a test',
        'content-email' => 'test mail {SANTA} => {TARGET}',
        'data-expiration' => date('Y-m-d', strtotime('+2 days')),
    ])
        ->assertSuccessful()
        ->assertJsonStructure(['message']);

    $draw = Draw::query()->find(1);
    assertEquals('foo', $draw->organizer_name);
    assertEquals('foo@foobar.com', $draw->organizer_email);
    assertNull($draw->organizer_participant_id);
    assertNull($draw->organizer);

    // Ensure Organizer receives his recap
    Notification::assertSentTimes(OrganizerRecapNotif::class, 1);
    Notification::assertSentTo(
        new AnonymousNotifiable,
        OrganizerRecapNotif::class,
        fn ($notification, $channels, $notifiable): bool => $notifiable->routes['mail'] === [$draw->organizer_email => $draw->organizer_name]
    );

    // Ensure Participants receive their own recap
    Notification::assertSentTimes(TargetDrawn::class, count($draw->participants));
    foreach ($draw->participants as $participant) {
        assertNotEquals($participant->email, $draw->organizer_email);
        Notification::assertSentTo($participant, TargetDrawn::class);
    }
});

it('sends to the organizer the link to their panel', function (): void {
    Notification::fake();

    ajaxPost('/', [
        'participant-organizer' => '0',
        'organizer' => ['name' => 'foo', 'email' => 'foo@foobar.com'],
        'participants' => generateParticipants(3),
        'title' => 'this is a test',
        'content-email' => 'test mail {SANTA} => {TARGET}',
        'data-expiration' => date('Y-m-d', strtotime('+2 days')),
    ])
        ->assertSuccessful()
        ->assertJsonStructure(['message']);

    $draw = Draw::query()->find(1);

    // Ensure Organizer receives his recap
    Notification::assertSentTo(
        new AnonymousNotifiable,
        OrganizerRecapNotif::class,
        function ($notification, $channels, $notifiable) use ($draw): bool {
            $link = $notification->toMail($notifiable)->data()['panelLink'];

            // Check the recap link is valid
            test()->get($link)->assertSuccessful();

            // Check link can be used for support
            assertEquals($draw->id, resolve(URLParser::class)->draw($link)?->id);

            return $notifiable->routes['mail'] === [$draw->organizer_email => $draw->organizer_name];
        }
    );
});

it('sends to the organizer the initial recap', function (): void {
    Notification::fake();

    ajaxPost('/', [
        'participant-organizer' => '0',
        'organizer' => ['name' => 'foo', 'email' => 'foo@foobar.com'],
        'participants' => generateParticipants(3),
        'title' => 'this is a test',
        'content-email' => 'test mail {SANTA} => {TARGET}',
        'data-expiration' => date('Y-m-d', strtotime('+2 days')),
    ])
        ->assertSuccessful()
        ->assertJsonStructure(['message']);

    $draw = Draw::query()->find(1);

    // Ensure Organizer receives his recap
    Notification::assertSentTo(
        new AnonymousNotifiable,
        OrganizerRecapNotif::class,
        function ($notification, $channels, $notifiable) use ($draw): true {
            $attachments = $notification->toMail($notifiable)->rawAttachments;

            assertCount(1, $attachments);
            assertEquals('text/csv', $attachments[0]['options']['mime']);

            // CSV have a BOM at start, remove it to parse, then check the amount of lines not starting with '#' (comments in CSV)
            $attachments[0]['data'] = str_replace("\xEF\xBB\xBF", '', $attachments[0]['data']);
            assertCount($draw->participants()->count(), collect(explode("\n", $attachments[0]['data']))
                ->map(fn ($line): array => str_getcsv($line, escape: '\\'))
                ->filter(fn ($data): bool => $data[0][0] !== '#')
            );

            return true;
        }
    );
});

it('can deal with thousands of participants', function (): void {
    assertEquals(0, Draw::query()->count());
    assertEquals(0, Participant::query()->count());

    $totalParticipants = 400;
    $participants = generateParticipants($totalParticipants, false);

    ajaxPost('/', [
        'participant-organizer' => '1',
        'participants' => $participants,
        'title' => 'this is a test',
        'content-email' => 'test mail {SANTA} => {TARGET}',
        'data-expiration' => date('Y-m-d', strtotime('+2 days')),
    ])
        ->assertSuccessful()
        ->assertJsonStructure(['message']);

    assertEquals(1, Draw::query()->count());
    assertEquals($participants[0]['name'], Draw::query()->find(1)->organizer_name);
    assertEquals($totalParticipants, Participant::query()->count());
})->group('massive');

it('records the participating organizer explicitly', function (): void {
    Notification::fake();

    ajaxPost('/', [
        'participant-organizer' => '1',
        'participants' => generateParticipants(3),
        'title' => 'this is a test',
        'content-email' => 'test mail {SANTA} => {TARGET}',
        'data-expiration' => date('Y-m-d', strtotime('+2 days')),
    ])->assertSuccessful();

    $draw = Draw::query()->firstOrFail();
    $first = $draw->participants->first();
    assertEquals($first->id, $draw->organizer_participant_id);
    expect($first->isOrganizer())->toBeTrue()
        ->and($draw->participants->get(1)->isOrganizer())->toBeFalse();

    // Still the organizer after changing their email, as the panel keeps it in sync
    $first->email = 'other@example.com';
    $first->save();

    expect($first->fresh()->isOrganizer())->toBeTrue();
});
