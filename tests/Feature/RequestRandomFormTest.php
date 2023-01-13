<?php

use App\Enums\AppMode;
use App\Models\Draw;
use App\Notifications\OrganizerRecap;
use App\Notifications\TargetDrawn;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

it('sends no notifications in case of error', function ($participants) {
    Notification::fake();

    createDraw($participants)
        ->assertUnprocessable();

    Notification::assertNothingSent();
})->with('invalid participants list');

it('sends notifications in case of success', function ($participants) {
    Notification::fake();

    $draw_id = createDraw($participants)
        ->assertSuccessful()
        ->original['draw'];

    $draw = Draw::find($draw_id);

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

    $draw_id = createDraw($participants, [
        'participant-organizer' => '0',
        'organizer' => ['name' => 'foo', 'email' => 'foo@foobar.com'],
    ])
        ->assertSuccessful()
        ->original['draw'];

    $draw = Draw::find($draw_id);

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

    $draw_id = createDraw($participants)
        ->assertSuccessful()
        ->original['draw'];

    $draw = Draw::find($draw_id);

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

    $draw_id = createDraw($participants)
        ->assertSuccessful()
        ->original['draw'];

    $draw = Draw::find($draw_id);

    // Ensure Organizer receives his recap
    Notification::assertSentTo($draw->organizer, OrganizerRecap::class, function ($notification, $channels, $notifiable) use ($draw) {
        $attachments = $notification->toMail($notifiable)->build()->rawAttachments;

        assertCount(1, $attachments);
        assertEquals('text/csv', $attachments[0]['options']['mime']);

        // CSV have a BOM at start, remove it to parse, then check the amount of lines not starting with '#' (comments in CSV)
        $attachments[0]['data'] = str_replace("\xEF\xBB\xBF", '', $attachments[0]['data']);
        assertCount($draw->participants()->count(), collect(explode("\n", $attachments[0]['data']))
            ->map(fn ($line) => str_getcsv($line))
            ->filter(fn ($data) => $data[0][0] !== '#')
        );

        return true;
    });
})->with('participants list');

it('respects limit in participants count', function ($participants) {
    Notification::fake();

    config()->set('modes.limitations.participants.'.(AppMode::FREE)->value, count($participants) - 1);

    createDraw($participants)
        ->assertUnprocessable()
        ->assertJsonValidationErrors([
            'participants',
        ]);

    config()->set('modes.limitations.participants.'.(AppMode::FREE)->value, count($participants));

    createDraw($participants)
        ->assertSuccessful();
})->with('participants list');

class RequestRandomFormTest extends TestCase
{
    /**
     * @group medium
     */
    public function it_can_deal_with_thousands_of_participants()
    {
        Notification::fake();

        $participants = [
            Collection::times(3000, function () {
                return ['name' => $this->faker->unique()->name, 'email' => $this->faker->safeEmail, 'exclusions' => []];
            })->toArray(),
        ];

        $draw_id = createDraw($participants)
            ->assertSuccessful()
            ->original['draw'];

        $draw = Draw::find($draw_id);

        assertEquals(count($participants), $draw->participants()->count());
    }
}
