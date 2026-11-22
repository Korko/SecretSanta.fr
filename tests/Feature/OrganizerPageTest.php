<?php

declare(strict_types=1);

use App\Models\Draw;
use App\Services\DrawFormHandler;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

// Taking part, the organizer has a single page: their panel, with their participant part in it

function organizerPanelUrl(Draw $draw): string
{
    return URL::signedRoute('organizerPanel', ['draw' => $draw->hash]);
}

test("the organizer's participant link leads to their panel, the other participants stay on their page", function (): void {
    Notification::fake();
    $draw = createServiceDraw(generateParticipants(3));
    [$organizer, $other] = $draw->participants;

    // Their earlier emails have this link: the browser keeps the #secret through the redirect
    $this->get(URL::signedRoute('dearSanta', ['participant' => $organizer->hash]))->assertRedirect(organizerPanelUrl($draw));
    $this->get(URL::signedRoute('dearSanta', ['participant' => $other->hash]))->assertOk()->assertViewIs('dearSanta');
});

test('the panel of a participating organizer holds their participant page', function (): void {
    Notification::fake();
    $draw = createServiceDraw(generateParticipants(3));
    $organizer = $draw->organizer;

    $routes = $this->get(organizerPanelUrl($draw))->assertOk()->viewData('participantRoutes');
    expect($routes['hash'])->toBe($organizer->hash);

    // With its own signed routes: the participant part loads and works from the panel
    ajaxGet($routes['fetchUrl'])->assertSuccessful()
        ->assertJsonPath('participant.hash', $organizer->hash)
        ->assertJsonPath('isOrganizer', true);
    ajaxPost($routes['contactUrl'], ['content' => 'Un pull ?'])->assertSuccessful();
    expect($organizer->dearSantas()->count())->toBe(1);
});

test('the panel holds no participant page for an organizer who does not take part, nor once the event is over', function (): void {
    Notification::fake();
    $notTakingPart = (new DrawFormHandler)
        ->withOrganizer(['name' => 'Orga', 'email' => 'orga@example.com'])
        ->withParticipants(generateParticipants(3))
        ->withExpiration(date('Y-m-d', strtotime('+2 days')))
        ->withTitle('test mail {SANTA} => {TARGET} title')
        ->withBody('test mail {SANTA} => {TARGET} body')
        ->save();
    $this->get(organizerPanelUrl($notTakingPart))->assertOk()->assertViewHas('participantRoutes', null);

    // Like the participant pages, which stop working then
    $ended = createServiceDraw(generateParticipants(3));
    $this->travelTo($ended->ends_at);
    $this->get(organizerPanelUrl($ended))->assertOk()->assertViewHas('participantRoutes', null);
});
