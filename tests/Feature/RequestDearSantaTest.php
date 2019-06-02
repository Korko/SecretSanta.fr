<?php

namespace Tests\Feature;

use Artisan;
use Mail;
use NoCaptcha;

class RequestDearSantaTest extends RequestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('migrate');
    }

    public function tearDown(): void
    {
        Artisan::call('migrate:reset');
        parent::tearDown();
    }

    public function testDearsanta()
    {
        Mail::fake();
        NoCaptcha::shouldReceive('verifyResponse')->andReturn(true);
        NoCaptcha::makePartial(); // We don't want to mock the display

        // Participants can only select one person, all the others will be excluded
        $participants = [
            [
                'name'   => 'toto',
                'email'  => 'test@test.com',
                'target' => 1,
            ],
            [
                'name'   => 'tata',
                'email'  => 'test2@test.com',
                'target' => 2,
            ],
            [
                'name'   => 'tutu',
                'email'  => 'test3@test.com',
                'target' => 0,
            ],
        ];

        // Initiate DearSanta
        $content = $this->ajaxPost('/', [
            'g-recaptcha-response' => 'mocked',
            'name'                 => array_column($participants, 'name'),
            'email'                => array_column($participants, 'email'),
            'phone'                => ['', '', ''],
            'exclusions'           => array_map(function ($id) use ($participants) {
                return array_values(array_map('strval', array_diff(array_keys($participants), [$id], [$participants[$id]['target']])));
            }, array_keys($participants)),
            'title'                => 'test mail title',
            'contentMail'          => 'test mail {SANTA} => {TARGET}',
            'contentSMS'           => '',
            'dearsanta'            => '1',
            'dearsanta-expiration' => date('Y-m-d', strtotime('+2 days')),
        ], 200);
        $this->assertEquals(['message' => 'Envoyé avec succès !'], $content);

        // For security issues, the key is only sent by mail and never stored
        // So fetch it from the mail
        $links = [];
        Mail::assertSent(\App\Mail\TargetDrawn::class, function ($mail) use (&$links) {
            $links[] = $mail->dearSantaLink;

            return true;
        });
        $this->assertEquals(count($participants), count($links));

        foreach ($links as $id => $link) {
            $path = parse_url($link, PHP_URL_PATH);
            $key = parse_url($link, PHP_URL_FRAGMENT);

            // Get the form page (just to check http code)
            $response = $this->get($path);
            $this->assertEquals(200, $response->status(), $response->__toString());

            // Try to contact santa
            $content = $this->ajaxPost($path, [
                'g-recaptcha-response' => 'mocked',
                'key'                  => $key,
                'title'                => 'test dearsanta mail title',
                'content'              => 'test dearsanta mail content',
            ], 200);
            $this->assertEquals(['message' => 'Envoyé avec succès !'], $content);

            Mail::assertSent(\App\Mail\DearSanta::class, function ($mail) use ($id, $participants) {
                $santaId = array_search($id, array_column($participants, 'target'));
                $santa = $participants[$santaId];

                return $mail->hasTo($santa['email']);
            });
        }
    }
}
