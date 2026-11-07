<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Draw;
use DrawCrypt;

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
