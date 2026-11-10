<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Draw;
use DrawCrypt;
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
