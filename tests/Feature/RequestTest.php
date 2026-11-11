<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Draw;
use DrawCrypt;
use Illuminate\Support\Facades\Date;

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

it('keeps security.txt valid: its Expires field is due for renewal a month ahead', function (): void {
    preg_match('/^Expires: (.+)$/m', (string) file_get_contents(public_path('.well-known/security.txt')), $match);

    // RFC 9116: required, and less than a year ahead. This fails a month before it lapses, to renew it in time
    expect($match[1] ?? null)->not->toBeNull()
        ->and(Date::parse($match[1])->isAfter(now()->addMonth()))->toBeTrue()
        ->and(Date::parse($match[1])->isBefore(now()->addYear()->addMonth()))->toBeTrue();
});
