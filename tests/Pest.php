<?php

use App\Managers\Encryption\SecretSantaEncryptionManager;
use App\Models\Draw\Draw;
use App\Models\Draw\Participant;

uses(
    Tests\TestCase::class,
    Illuminate\Foundation\Testing\RefreshDatabase::class,
    Illuminate\Foundation\Testing\WithFaker::class
)->in('Feature', 'Unit');

uses()->group('browser')->in('Browser');

uses()->beforeEach(function () {
    // Configuration globale avant chaque test
    \Illuminate\Support\Facades\Cache::flush();
    \Illuminate\Support\Facades\Redis::flushdb();
})->in('Feature');

// Helpers globaux
function createDraw(array $attributes = []): Draw
{
    $encryptionManager = app(SecretSantaEncryptionManager::class);
    $encryption = $encryptionManager->createDrawEncryption();

    return Draw::factory()->create(array_merge([
        'organizer_key_hash' => $encryption['organizer_key_hash'],
        'master_key_encrypted' => $encryption['master_key_encrypted'],
    ], $attributes));
}

function createParticipant(Draw $draw, array $attributes = []): Participant
{
    return Participant::factory()->create(array_merge([
        'draw_id' => $draw->id,
        'status' => 'accepted',
    ], $attributes));
}
