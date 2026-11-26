<?php

declare(strict_types=1);

use App\Models\Draw;
use App\Notifications\TargetDrawn;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

// "Le jour J": the place and the budget per gift, both optional, encrypted like the rest of the draw

function eventForm(array $extra = []): array
{
    return [
        'participant-organizer' => '1',
        'participants' => generateParticipants(3),
        'title' => 'this is a test',
        'content-email' => 'test mail {SANTA} => {TARGET}',
        'data-expiration' => date('Y-m-d', strtotime('+2 days')),
        ...$extra,
    ];
}

it('stores the location and the budget, encrypted', function (): void {
    Notification::fake();

    ajaxPost('/', eventForm(['data-location' => '  Chez Mamie  ', 'data-budget' => '20']))->assertSuccessful();

    $draw = Draw::query()->firstOrFail();
    expect($draw->location)->toBe('Chez Mamie')
        ->and($draw->budget)->toBe('20');

    $raw = DB::table('draws')->where('id', $draw->id)->first();
    expect($raw->location)->not->toContain('Mamie')
        ->and($raw->budget)->not->toBe('20');
});

it('accepts a draw without location nor budget', function (): void {
    Notification::fake();

    ajaxPost('/', eventForm(['data-location' => '', 'data-budget' => '']))->assertSuccessful();

    $draw = Draw::query()->firstOrFail();
    expect($draw->location)->toBeNull()
        ->and($draw->budget)->toBeNull();
});

it('refuses an invalid budget or a too long location', function (array $extra, string $field): void {
    Notification::fake();

    ajaxPost('/', eventForm($extra))->assertStatus(422)->assertJsonValidationErrors($field);

    expect(Draw::query()->count())->toBe(0);
})->with([
    'not a number' => [['data-budget' => 'vingt'], 'data-budget'],
    'negative' => [['data-budget' => '-5'], 'data-budget'],
    'too long location' => [['data-location' => str_repeat('a', 256)], 'data-location'],
]);

it('shows the date, the location and the budget in the draw email, only when given', function (): void {
    Notification::fake();

    ajaxPost('/', eventForm(['data-location' => 'Chez Mamie', 'data-budget' => '20']))->assertSuccessful();

    $draw = Draw::query()->firstOrFail();
    Notification::assertSentTo($draw->participants->get(1), TargetDrawn::class, function (TargetDrawn $notification, $channels, $notifiable): bool {
        $html = (string) $notification->toMail($notifiable)->render();

        return str_contains($html, 'Chez Mamie') && preg_match('/20\s?€/u', $html) === 1;
    });

    $draw->update(['location' => null, 'budget' => null]);
    $participant = $draw->fresh()->participants->get(1);
    $html = (string) (new TargetDrawn)->toMail($participant)->render();
    expect($html)->not->toContain(__('emails.event.location', ['location' => '']))
        ->and($html)->not->toContain(__('emails.event.budget', ['budget' => '']));
});

test('the organizer can change the location and the budget', function (): void {
    $draw = Draw::factory()->hasParticipants(3)->create();
    $path = URL::signedRoute('organizerPanel.changeEventDetails', ['draw' => $draw]);

    ajaxPost($path, ['location' => 'Au bureau', 'budget' => '15.50'])
        ->assertSuccessful()
        ->assertJson(['location' => 'Au bureau', 'budget' => '15.50']);
    expect($draw->refresh()->location)->toBe('Au bureau');

    expect(ajaxGet(URL::signedRoute('organizerPanel.fetch', ['draw' => $draw]))->json())
        ->toMatchArray(['location' => 'Au bureau', 'budget' => '15.50']);

    // Only the field sent changes, an empty one removes it
    ajaxPost($path, ['location' => ''])->assertSuccessful();
    expect($draw->refresh()->location)->toBeNull()
        ->and($draw->budget)->toBe('15.50');

    ajaxPost($path, ['budget' => 'beaucoup'])->assertStatus(422);
    expect($draw->refresh()->budget)->toBe('15.50');
});

test('the organizer cannot change the location of an expired draw', function (): void {
    $draw = Draw::factory()->hasParticipants(3)->expired()->create();

    ajaxPost(URL::signedRoute('organizerPanel.changeEventDetails', ['draw' => $draw]), ['location' => 'Ailleurs'])
        ->assertForbidden();
});
