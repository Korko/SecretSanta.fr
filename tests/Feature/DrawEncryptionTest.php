<?php

declare(strict_types=1);

use App\Facades\DrawCrypt;
use App\Models\Participant;
use App\Services\DrawEncrypter;
use App\Services\LegacyDrawEncrypter;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\DB;

function drawTestAppKey(): string
{
    return base64_decode(mb_substr(config()->string('app.key'), 7));
}

test('the draw data cannot be decrypted with APP_KEY alone', function (): void {
    $secret = random_bytes(16);
    $encrypter = new DrawEncrypter(drawTestAppKey());
    $encrypter->setIV($secret);
    $payload = $encrypter->encrypt('a long enough secret santa message to span several AES blocks');

    // Bypass the MAC and try to decrypt the raw value with APP_KEY, as the previous scheme allowed
    $data = json_decode(base64_decode($payload), true);
    $raw = openssl_decrypt($data['value'], 'AES-256-CBC', drawTestAppKey(), 0, base64_decode($data['iv']));
    expect($raw === false || ! str_contains($raw, 'santa message'))->toBeTrue();

    $wrongSecret = new DrawEncrypter(drawTestAppKey());
    $wrongSecret->setIV(random_bytes(16));
    expect(fn () => $wrongSecret->decrypt($payload))->toThrow(DecryptException::class);

    $rightSecret = new DrawEncrypter(drawTestAppKey());
    $rightSecret->setIV($secret);
    expect($rightSecret->decrypt($payload))->toBe('a long enough secret santa message to span several AES blocks');
});

test('the same value is encrypted differently each time', function (): void {
    $encrypter = new DrawEncrypter(drawTestAppKey());

    expect($encrypter->encrypt('same value'))->not->toBe($encrypter->encrypt('same value'));
});

test('the draw secret keeps its length so existing links stay valid', function (): void {
    expect(mb_strlen(new DrawEncrypter(drawTestAppKey())->getIV(), '8bit'))->toBe(16)
        ->and(fn () => new DrawEncrypter(drawTestAppKey())->setIV(random_bytes(32)))->toThrow(RuntimeException::class);
});

test('data written by the previous scheme is still readable and upgraded on save', function (): void {
    $draw = createServiceDraw(generateParticipants(3));
    $participant = $draw->participants->first();
    $name = $participant->name;

    $legacy = new LegacyDrawEncrypter(drawTestAppKey(), config()->string('app.cipher'));
    $legacy->setIV(DrawCrypt::getIV());
    DB::table('participants')->where('id', $participant->id)->update(['name' => $legacy->encrypt($name)]);

    $participant = Participant::query()->find($participant->id);
    expect($participant->name)->toBe($name);

    $participant->name = 'Renamed';
    $participant->save();

    $stored = json_decode(base64_decode((string) DB::table('participants')->where('id', $participant->id)->value('name')), true);
    expect($stored)->toHaveKey('iv')
        ->and(Participant::query()->find($participant->id)->name)->toBe('Renamed');
});
