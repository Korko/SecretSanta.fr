<?php

namespace Tests\Feature;

use DrawHandler;

class RequestTest extends RequestCase
{
    use \Illuminate\Foundation\Testing\DatabaseMigrations;
    use \Illuminate\Foundation\Testing\DatabaseTransactions;

    public function testServiceFormat(): void
    {
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

        $this->ajaxPost('/', [
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
    }
}
