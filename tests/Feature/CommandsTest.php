<?php

declare(strict_types=1);

use App\Models\Draw;
use App\Models\ParticipantLimit;
use App\Notifications\TargetDrawn;
use App\Notifications\TargetWithdrawn;
use App\Services\EmailClient;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

// The link a participant got, as an administrator would paste it
function participantLink(Draw $draw): string
{
    return URL::signedRoute('dearSanta', ['participant' => $draw->participants->first()->hash]).'#'.base64_encode(DrawCrypt::getSecret());
}

it('withdraws a participant like the organizer panel does', function (): void {
    Notification::fake();
    $draw = Draw::factory()->hasParticipants(4)->create();
    $participant = $draw->participants->last();
    $santa = $participant->santa;

    $this->artisan('secretsanta:withdraw-participant', ['url' => participantLink($draw), 'id' => $participant->id])
        ->expectsConfirmation('Withdraw '.$participant->name.' (#'.$participant->id.') and email their santa their new target?', 'yes')
        ->assertSuccessful();

    expect($participant->fresh())->toBeNull()
        ->and($draw->participants()->count())->toBe(3);
    Notification::assertSentTo($santa, TargetWithdrawn::class);
});

it('keeps at least 3 participants when withdrawing one from the command line', function (): void {
    Notification::fake();
    $draw = Draw::factory()->hasParticipants(3)->create();

    $this->artisan('secretsanta:withdraw-participant', ['url' => participantLink($draw), 'id' => $draw->participants->last()->id, '--force' => true])
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

// These change a draw or email people with no organizer in the loop: "no", the default, leaves everything as it was
it('asks before withdrawing a participant', function (): void {
    Notification::fake();
    $draw = Draw::factory()->hasParticipants(4)->create();
    $participant = $draw->participants->last();
    Notification::fake(); // Only what the command sends from here

    $this->artisan('secretsanta:withdraw-participant', ['url' => participantLink($draw), 'id' => $participant->id])
        ->expectsConfirmation('Withdraw '.$participant->name.' (#'.$participant->id.') and email their santa their new target?', 'no')
        ->expectsOutputToContain('Cancelled')
        ->assertSuccessful();

    expect($participant->fresh())->not->toBeNull()
        ->and($draw->participants()->count())->toBe(4);
    Notification::assertNothingSent();
});

it('asks before fixing a participant email', function (): void {
    Notification::fake();
    $draw = Draw::factory()->hasParticipants(3)->create();
    $participant = $draw->participants->last();
    $email = $participant->email;
    Notification::fake();

    $this->artisan('secretsanta:fix-participant', ['url' => participantLink($draw), 'id' => $participant->id, 'email' => 'fixed@example.com'])
        ->expectsConfirmation('Change the email of '.$participant->name.' (#'.$participant->id.') from '.$email.' to fixed@example.com and send them their target again?', 'no')
        ->assertSuccessful();

    expect($participant->fresh()->email)->toBe($email);
    Notification::assertNothingSent();

    $this->artisan('secretsanta:fix-participant', ['url' => participantLink($draw), 'id' => $participant->id, 'email' => 'fixed@example.com', '--force' => true])
        ->assertSuccessful();

    expect($participant->fresh()->email)->toBe('fixed@example.com');
    Notification::assertSentToTimes($participant, TargetDrawn::class, 1);
});

it('asks before replacing a participant', function (): void {
    Notification::fake();
    $draw = Draw::factory()->hasParticipants(3)->create();
    $participant = $draw->participants->last();
    $participant->dearSantas()->create(['mail_body' => 'Un pull ?']);
    [$name, $email] = [$participant->name, $participant->email];
    Notification::fake();

    $this->artisan('secretsanta:change-participant', ['url' => participantLink($draw), 'id' => $participant->id, 'name' => 'Someone else', 'email' => 'else@example.com'])
        ->expectsConfirmation('Replace '.$name.' <'.$email.'> (#'.$participant->id.') by Someone else <else@example.com>, delete the messages they wrote to their santa and email both?', 'no')
        ->assertSuccessful();

    expect($participant->fresh())->name->toBe($name)->email->toBe($email)
        ->and($participant->dearSantas()->count())->toBe(1);
    Notification::assertNothingSent();

    $this->artisan('secretsanta:change-participant', ['url' => participantLink($draw), 'id' => $participant->id, 'name' => 'Someone else', 'email' => 'else@example.com', '--force' => true])
        ->assertSuccessful();

    expect($participant->fresh())->name->toBe('Someone else')->email->toBe('else@example.com')
        ->and($participant->dearSantas()->count())->toBe(0);
    Notification::assertSentTo($participant, TargetDrawn::class);
    Notification::assertSentTo($participant->santa, TargetWithdrawn::class);
});

it('asks before sending a whole draw again', function (): void {
    Notification::fake();
    $draw = Draw::factory()->hasParticipants(3)->create();
    Notification::fake();

    $this->artisan('secretsanta:fix-draw', ['url' => participantLink($draw)])
        ->expectsConfirmation('Send their target again to the 3 participants of draw #'.$draw->id.'?', 'no')
        ->assertSuccessful();

    Notification::assertNothingSent();

    $this->artisan('secretsanta:fix-draw', ['url' => participantLink($draw), '--force' => true])->assertSuccessful();

    Notification::assertSentTimes(TargetDrawn::class, 3);
});

it('asks before resending the bounced emails of blacklisted domains', function (): void {
    $this->mock(EmailClient::class)->shouldNotReceive('getMails');

    $this->artisan('secretsanta:post-blacklist', ['folder' => 'Bounces', 'domains' => ['example.com', 'example.org']])
        ->expectsConfirmation('Send their emails again to the participants on example.com, example.org bounced in Bounces, and delete those bounces?', 'no')
        ->assertSuccessful();
});

it('asks before revoking an administrator link', function (): void {
    [$id] = ParticipantLimit::generate(80);

    $this->artisan('secretsanta:revoke-participant-limit', ['id' => $id])
        ->expectsConfirmation('Delete link #'.$id.' (up to 80 participants, active)?', 'no')
        ->assertSuccessful();

    expect(ParticipantLimit::query()->find($id))->not->toBeNull();
});
