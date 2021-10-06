<?php

namespace Tests\Feature;

use DrawCrypt;
use DrawHandler;

/*it('handles ajax format', function () {
    $mock = DrawHandler::partialMock();

    $mock->shouldReceive('toParticipants')
        ->once()
        ->with([
            ['name' => 'toto', 'email' => 'test@test.com', 'exclusions' => []],
            ['name' => 'tata', 'email' => 'test3@test.com', 'exclusions' => [1, 2]],
            ['name' => 'tutu', 'email' => 'test2@test.com', 'exclusions' => [0]],
        ])
        ->andReturnSelf();

    $mock->shouldReceive('sendMail')
        ->once()
        ->andReturn(true);

    ajaxPost('/', [
        'participants'         => [
            [
                'name'       => 'toto',
                'email'      => 'test@test.com',
                'exclusions' => [],
            ],
            [
                'name'       => 'tata',
                'email'      => 'test3@test.com',
                'exclusions' => ['1', '2'],
            ],
            [
                'name'       => 'tutu',
                'email'      => 'test2@test.com',
                'exclusions' => ['0'],
            ],
        ],
        'title'                => 'test mail title',
        'content-email'        => 'test mail {SANTA} => {TARGET}',
        'data-expiration'      => date('Y-m-d', strtotime('+2 days')),
    ]);
});*/

it('stores database crypted entries', function () {
    $draw = createDrawWithParticipants(3);

    $decrypted = $draw->participants[0]->name;

    DrawCrypt::shouldReceive('decrypt')
        ->andReturnArg(0);

    assertNotEquals($draw->participants[0]->name, $decrypted);
});
