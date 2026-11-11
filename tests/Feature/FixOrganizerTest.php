<?php

declare(strict_types=1);

use App\Enums\DeliveryStatus;
use App\Models\Draw;
use App\Notifications\OrganizerEmailChanged;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Testing\TestResponse;

function fixOrganizer(Draw $draw, string $email): TestResponse
{
    $url = URL::signedRoute('dearSanta', ['participant' => $draw->participants->last()->hash]).'#'.base64_encode(DrawCrypt::getIV());

    return ajaxPost(URL::route('fixOrganizer.handle'), ['url' => $url, 'email' => $email]);
}

test('the organizer can fix a mistyped address until they received their email', function (): void {
    Notification::fake();
    $draw = createServiceDraw(generateParticipants(3));
    $draw->update(['organizer_name' => 'Orga', 'organizer_email' => 'orga@exampel.com']);
    $this->travel(10)->minutes();

    fixOrganizer($draw, 'orga@example.com')->assertSuccessful();
    // The previous address is told, in case it wasn't them
    Notification::assertSentOnDemand(OrganizerEmailChanged::class, fn ($n, $c, $notifiable): bool => array_key_exists('orga@exampel.com', $notifiable->routes['mail']));
    expect($draw->fresh()->organizer_email)->toBe('orga@example.com');

    // Opening the panel proves the address works: /fix can't redirect it anymore
    $this->travel(10)->minutes();
    ajaxGet(URL::signedRoute('organizerPanel.fetch', ['draw' => $draw]))->assertSuccessful();
    fixOrganizer($draw, 'orga@exampl.com')->assertStatus(422)->assertJsonValidationErrors('email');
    expect($draw->fresh()->organizer_email)->toBe('orga@example.com');
});

test('a participating organizer whose email was received cannot be fixed', function (): void {
    Notification::fake();
    $draw = createServiceDraw(generateParticipants(3));
    $organizer = $draw->participants->first();
    $draw->update(['organizer_name' => $organizer->name, 'organizer_email' => $organizer->email]);
    $organizer->mail->updateDeliveryStatus(DeliveryStatus::Received);
    $this->travel(10)->minutes();

    fixOrganizer($draw->fresh(), $organizer->email.'x')->assertStatus(422)->assertJsonValidationErrors('email');
});

test('an organizer whose recap was confirmed received cannot be fixed either', function (): void {
    Notification::fake();
    $draw = createServiceDraw(generateParticipants(3));
    $draw->update(['organizer_name' => 'Orga', 'organizer_email' => 'orga@exampel.com']);
    // A read receipt or an auto-reply on the recap's tracked return path
    $draw->recapMail()->markAsReceived();
    $this->travel(10)->minutes();

    fixOrganizer($draw->fresh(), 'orga@example.com')->assertStatus(422)->assertJsonValidationErrors('email');
    expect($draw->fresh()->organizer_email)->toBe('orga@exampel.com');
});
