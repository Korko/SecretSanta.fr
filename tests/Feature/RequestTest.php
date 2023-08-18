<?php

namespace Tests\Feature;

use App\Facades\DrawCrypt;
use App\Models\Participant;

it('stores database crypted entries', function () {
    $participant = Participant::factory()
        ->create();

    $decrypted = $participant->name;

    // Disable decryption
    DrawCrypt::shouldReceive('decrypt')
        ->andReturnArg(0);

    $this->assertNotEquals($participant->name, $decrypted);
});
