<?php

declare(strict_types=1);

use App\Models\Draw;
use App\Models\Participant;
use App\Models\ParticipantLimit;
use App\Notifications\OrganizerRecap as OrganizerRecapNotif;
use App\Notifications\TargetDrawn;
use App\Services\URLParser;
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
    Notification::assertNotSentTo(Draw::query()->firstOrFail(), OrganizerRecapNotif::class);
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
        $draw,
        OrganizerRecapNotif::class,
        fn ($notification, $channels, $notifiable): bool => $notifiable->routeNotificationForMail() === [$draw->organizer_email => $draw->organizer_name]
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
        $draw,
        OrganizerRecapNotif::class,
        function ($notification, $channels, $notifiable) use ($draw): bool {
            $link = $notification->toMail($notifiable)->data()['panelLink'];

            // Check the recap link is valid
            test()->get($link)->assertSuccessful();

            // Check link can be used for support
            assertEquals($draw->id, resolve(URLParser::class)->draw($link)?->id);

            return $notifiable->routeNotificationForMail() === [$draw->organizer_email => $draw->organizer_name];
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
        $draw,
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
    // Above the default limit: an administrator can raise it (MAX_PARTICIPANTS)
    config(['app.max_participants' => 400]);

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

it('limits the number of draws per hour from the same address, refused forms aside', function (): void {
    Notification::fake();

    $draw = fn (): array => [
        'participant-organizer' => '1',
        'participants' => generateParticipants(3),
        'title' => 'this is a test',
        'content-email' => 'test mail {SANTA} => {TARGET}',
        'data-expiration' => date('Y-m-d', strtotime('+2 days')),
    ];

    for ($i = 0; $i < 25; $i++) {
        ajaxPost('/', [])->assertStatus(422);
    }

    for ($i = 0; $i < 20; $i++) {
        ajaxPost('/', $draw())->assertSuccessful();
    }

    ajaxPost('/', $draw())->assertStatus(429)->assertJson(['message' => trans('error.throttle')]);
});

it('limits the number of participants and says who to contact', function (): void {
    Notification::fake();
    config(['app.max_participants' => 4, 'app.email' => 'admin@example.com']);

    ajaxPost('/', [
        'participant-organizer' => '1',
        'participants' => generateParticipants(5, false),
        'title' => 'this is a test',
        'content-email' => 'test mail {SANTA} => {TARGET}',
        'data-expiration' => date('Y-m-d', strtotime('+2 days')),
    ])
        ->assertStatus(422)
        ->assertJsonValidationErrors('participants')
        ->assertJsonFragment(['participants' => [trans('validation.custom.randomform.participants.max', ['max' => 4, 'email' => 'admin@example.com'])]]);

    assertEquals(0, Draw::query()->count());
    Notification::assertNothingSent();
});

it('lets an administrator link raise the limit for a single draw', function (): void {
    Notification::fake();
    config(['app.max_participants' => 4]);

    [, $code] = ParticipantLimit::generate(6);
    $draw = fn (): array => [
        'limit' => $code,
        'participant-organizer' => '1',
        'participants' => generateParticipants(6, false),
        'title' => 'this is a test',
        'content-email' => 'test mail {SANTA} => {TARGET}',
        'data-expiration' => date('Y-m-d', strtotime('+2 days')),
    ];

    $this->get('/?limit='.$code)->assertViewHas('maxParticipants', 6)->assertViewHas('limitCode', $code);

    ajaxPost('/', $draw())->assertSuccessful();
    assertEquals(6, Draw::query()->firstOrFail()->max_participants);

    // Used: back to the usual limit, and the page says so
    ajaxPost('/', $draw())->assertStatus(422)->assertJsonValidationErrors('participants');
    $this->get('/?limit='.$code)->assertViewHas('maxParticipants', 4)->assertViewHas('limitCode', null)->assertViewHas('limitInvalid', true);
    assertEquals(1, Draw::query()->count());
});

it('creates the administrator links from the command line', function (): void {
    config(['app.max_participants' => 50]);

    $this->artisan('secretsanta:allow-participants', ['max' => 50])->assertFailed();
    assertEquals(0, ParticipantLimit::query()->count());

    $this->artisan('secretsanta:allow-participants', ['max' => 120])->assertSuccessful();
    assertEquals(120, ParticipantLimit::query()->sole()->max);
});

it('lists the administrator links and revokes them', function (): void {
    [$active] = ParticipantLimit::generate(80);
    [$used] = ParticipantLimit::generate(100);
    ParticipantLimit::query()->findOrFail($used)->consume(createServiceDraw(generateParticipants(3)));

    // One expectation per run: a matched output line is consumed, and the table is written at once
    $this->artisan('secretsanta:participant-limits')->expectsOutputToContain('active')->assertSuccessful();
    $this->artisan('secretsanta:participant-limits')->expectsOutputToContain('used')->assertSuccessful();

    $this->artisan('secretsanta:revoke-participant-limit', ['id' => $active])->assertSuccessful();
    $this->artisan('secretsanta:revoke-participant-limit', ['id' => $active])->assertFailed();
    assertEquals([$used], ParticipantLimit::query()->pluck('id')->all());
});

it('deletes a used administrator link with its draw', function (): void {
    [$id] = ParticipantLimit::generate(80);
    $draw = createServiceDraw(generateParticipants(3));
    ParticipantLimit::query()->findOrFail($id)->consume($draw);

    $draw->delete();

    expect(ParticipantLimit::query()->find($id))->toBeNull();
});
