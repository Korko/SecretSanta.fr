<?php

namespace Tests\Feature;

use DrawCrypt;

it('stores database crypted entries', function () {
    $draw = createDatabaseDraw(3);

    $decrypted = $draw->participants[0]->name;

    DrawCrypt::shouldReceive('decrypt')
        ->andReturnArg(0);

    assertNotEquals($draw->participants[0]->name, $decrypted);
});
