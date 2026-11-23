<?php

declare(strict_types=1);

use App\Models\Draw;
use App\Models\Participant;
use App\Notifications\TargetChanged;
use App\Notifications\TargetDrawn;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Testing\TestResponse;

// A participant who withdrew is kept aside: the organizer can bring them back, when someone else had got their
// email (a mistyped address) and withdrawn in their place

function withdraw(Participant $participant): void
{
    ajaxPost(URL::signedRoute('dearSanta.withdraw', ['participant' => $participant]))->assertSuccessful();
}

function restore(Draw $draw, Participant $participant, string $email, bool $force = false): TestResponse
{
    return ajaxPost(URL::signedRoute('organizerPanel.restore', ['draw' => $draw]), ['participant' => $participant->hash, 'email' => $email] + ($force ? ['force' => 1] : []));
}

// Everyone gives to one other person and is given to by one
function expectValidPairs(Draw $draw, int $count): void
{
    $participants = $draw->participants()->with('target')->get();
    expect($participants)->toHaveCount($count)
        ->and($participants->map(fn (Participant $participant): int => $participant->target->id)->unique())->toHaveCount($count)
        ->and($participants->every(fn (Participant $participant): bool => $participant->target->isNot($participant)))->toBeTrue();
}

test('the organizer brings back someone who withdrew, at the right address', function (): void {
    Notification::fake();
    $draw = Draw::factory()->hasParticipants(5)->create();
    $participant = $draw->participants->first();
    withdraw($participant);

    ajaxGet(URL::signedRoute('organizerPanel.fetch', ['draw' => $draw]))
        ->assertJsonCount(4, 'participants')
        ->assertJsonPath('withdrawn.0.hash', $participant->hash)
        ->assertJsonPath('withdrawn.0.name', $participant->name);
    expectValidPairs($draw, 4);

    restore($draw, $participant, 'right@example.com')
        ->assertSuccessful()
        ->assertJsonCount(5, 'participants')
        ->assertJsonCount(0, 'withdrawn');

    $participant->refresh();
    expect($participant->withdrawn_at)->toBeNull()
        ->and($participant->email)->toBe('right@example.com');
    expectValidPairs($draw, 5);

    // Their target at the right address, and one santa now gives to them
    Notification::assertSentTo($participant, TargetDrawn::class, fn ($n, $c, $notifiable): bool => $notifiable->email === 'right@example.com');
    Notification::assertSentTo($participant->santa, TargetChanged::class);
    // Their page works again
    ajaxGet(URL::signedRoute('dearSanta.fetch', ['participant' => $participant]))->assertSuccessful();
});

test('bringing someone back respects their exclusions, unless the organizer forces it', function (): void {
    Notification::fake();
    $draw = Draw::factory()->hasParticipants(4)->create();
    $participant = $draw->participants->first();
    withdraw($participant);

    // They exclude everyone left: no place for them
    $participant->exclusions()->attach($draw->participants()->pluck('id'));

    restore($draw, $participant, $participant->email)->assertStatus(422)->assertJsonValidationErrors('exclusions');
    expect($participant->fresh()->withdrawn_at)->not->toBeNull();

    restore($draw, $participant, $participant->email, force: true)->assertSuccessful();
    expect($participant->fresh()->withdrawn_at)->toBeNull();
    expectValidPairs($draw, 4);
});

test('bringing someone back is refused for a name taken since, a refused address or an event over', function (): void {
    Notification::fake();
    $draw = Draw::factory()->hasParticipants(4)->create();
    $participant = $draw->participants->first();
    withdraw($participant);

    $other = $draw->participants()->first();
    $other->name = $participant->name;
    $other->save();
    restore($draw, $participant, $participant->email)->assertStatus(422)->assertJsonValidationErrors('participant');
    $other->name = 'Someone else';
    $other->save();

    $draw->refuseEmail('reported@example.com');
    restore($draw, $participant, 'reported@example.com')->assertStatus(422)->assertJsonValidationErrors('email');

    $this->travelTo($draw->ends_at);
    restore($draw, $participant, $participant->email)->assertForbidden()->assertJson(['expired' => true]);
    expect($participant->fresh()->withdrawn_at)->not->toBeNull();
});

test('who withdrew is left out of the recap and its exclusions, and deleted with the draw', function (): void {
    Notification::fake();
    $draw = Draw::factory()->hasParticipants(4)->create();
    [$participant, $other] = $draw->participants;
    $other->exclusions()->attach($participant);
    withdraw($participant);

    expect($draw->fresh()->initialCsv())->not->toContain($participant->name);

    $draw->fresh()->delete();
    expect(Participant::query()->whereKey($participant->id)->exists())->toBeFalse();
});
