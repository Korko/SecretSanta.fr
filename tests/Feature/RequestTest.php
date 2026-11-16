<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Draw;
use DrawCrypt;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;

use function PHPUnit\Framework\assertNotEquals;

it('stores database crypted entries', function (): void {
    $draw = Draw::factory()
        ->hasParticipants(3)
        ->create();

    $decrypted = $draw->participants[0]->name;

    DrawCrypt::shouldReceive('decrypt')
        ->andReturnArg(0);

    assertNotEquals($draw->participants[0]->name, $decrypted);
});

it('picks the language from the domain', function (string $host, string $lang): void {
    config(['app.domains' => ['fr' => 'example.fr', 'en' => 'example.com']]);

    $this->get("https://{$host}/")
        ->assertSuccessful()
        ->assertSee('<html class="no-js" lang="'.$lang.'">', false);
})->with([
    ['example.fr', 'fr'],
    ['www.example.fr', 'fr'],
    ['example.com', 'en'],
    ['localhost', 'fr'],
]);

it('translates the 404 page', function (string $lang, string $back): void {
    App::setLocale($lang);

    $this->view('errors.404')->assertSee($back);
})->with([
    ['fr', 'Revenir sur la page d'],
    ['en', 'Back to the home page'],
    ['de', 'Zurück zur Startseite'],
]);

it('sends the security headers', function (): void {
    $this->get('/')
        ->assertSuccessful()
        ->assertHeader('Referrer-Policy', 'same-origin')
        ->assertHeader('X-Frame-Options', 'DENY')
        ->assertHeader('X-Content-Type-Options', 'nosniff');
});

it('only lets the site scripts run, the inline one through its nonce', function (): void {
    $response = $this->get('/')->assertSuccessful();

    $csp = (string) $response->headers->get('Content-Security-Policy');
    expect($csp)->toContain("frame-ancestors 'none'", "object-src 'none'", "base-uri 'self'")
        ->and($csp)->toMatch("/script-src 'self' 'nonce-[^']+' 'unsafe-eval'/")
        ->and($csp)->not->toContain('https:', '*');

    // The @javascript block (window.global) carries the nonce of the header, a new one on each response
    preg_match("/'nonce-([^']+)'/", $csp, $nonce);
    $response->assertSee('<script nonce="'.$nonce[1].'">', false);
    expect($this->get('/')->headers->get('Content-Security-Policy'))->not->toContain($nonce[1]);
});

it('answers an altered link with a client error, not a server one', function (): void {
    $draw = Draw::factory()->hasParticipants(3)->create();
    $panel = URL::signedRoute('organizerPanel', ['draw' => $draw]);
    $fetch = URL::signedRoute('organizerPanel.fetch', ['draw' => $draw]);

    $this->get($panel.'x')->assertForbidden()->assertViewIs('missingDraw');
    ajaxGet($fetch.'x')->assertForbidden()->assertJsonPath('message', trans('error.signature'));

    // The IV is the URL hash: truncated or altered
    ajaxGet($fetch, ['X-HASH-IV' => base64_encode('short')])->assertForbidden();
    ajaxGet($fetch, ['X-HASH-IV' => base64_encode(random_bytes(mb_strlen(DrawCrypt::getIV(), '8bit')))])->assertForbidden();
});
