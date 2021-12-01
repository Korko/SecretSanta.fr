<?php

namespace Tests\Feature;

use App\Facades\DrawCrypt;
use App\Models\Draw;

it('stores database crypted entries', function () {
    $draw = Draw::factory()
        ->hasParticipants(3)
        ->create();

    $decrypted = $draw->participants[0]->name;

    DrawCrypt::shouldReceive('decrypt')
        ->andReturnArg(0);

    assertNotEquals($draw->participants[0]->name, $decrypted);
});
