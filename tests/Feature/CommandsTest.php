<?php

declare(strict_types=1);

use App\Models\Draw;
use App\Notifications\TargetWithdrawn;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

// The link a participant got, as an administrator would paste it
function participantLink(Draw $draw): string
{
    return URL::signedRoute('dearSanta', ['participant' => $draw->participants->first()->hash]).'#'.base64_encode(DrawCrypt::getIV());
}

it('withdraws a participant like the organizer panel does', function (): void {
    Notification::fake();
    $draw = Draw::factory()->hasParticipants(4)->create();
    $participant = $draw->participants->last();
    $santa = $participant->santa;

    $this->artisan('secretsanta:withdraw-participant', ['url' => participantLink($draw), 'id' => $participant->id])->assertSuccessful();

    expect($participant->fresh())->toBeNull()
        ->and($draw->participants()->count())->toBe(3);
    Notification::assertSentTo($santa, TargetWithdrawn::class);
});

it('keeps at least 3 participants when withdrawing one from the command line', function (): void {
    Notification::fake();
    $draw = Draw::factory()->hasParticipants(3)->create();

    $this->artisan('secretsanta:withdraw-participant', ['url' => participantLink($draw), 'id' => $draw->participants->last()->id])
        ->expectsOutputToContain(trans('error.withdraw'));

    expect($draw->participants()->count())->toBe(3);
});

it('shows a draw with the targets, the messages and the body on demand', function (): void {
    Notification::fake();
    $draw = Draw::factory()->hasParticipants(3)->create();

    $this->artisan('secretsanta:list-draw', ['url' => participantLink($draw), '--targets' => true, '--messages' => true, '--body' => true])
        ->expectsOutputToContain('Messages received')
        ->assertSuccessful();
});
