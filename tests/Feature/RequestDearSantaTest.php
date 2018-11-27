<?php

namespace Tests\Feature;

use Artisan;
use Korko\SecretSanta\Draw;
use Korko\SecretSanta\Participant;
use Mail;
use Metrics;
use Mockery;
use NoCaptcha;

class RequestDearSantaTest extends RequestCase
{
    public function setUp()
    {
        parent::setUp();
        Artisan::call('migrate');
    }

    public function tearDown()
    {
        Artisan::call('migrate:reset');
        parent::tearDown();
    }

    public function testDearsanta()
    {
        Mail::fake();
        NoCaptcha::shouldReceive('verifyResponse')->andReturn(true);
        NoCaptcha::makePartial(); // We don't want to mock the display

        // Initiate DearSanta
        $content = $this->ajaxPost('/', [
            'g-recaptcha-response' => 'mocked',
            'name'                 => ['toto', 'tata', 'tutu'],
            'email'                => ['test@test.com', 'test2@test.com', 'test3@test.com'],
            'phone'                => ['', '', ''],
            'exclusions'           => [['2']],
            'title'                => 'test mail title',
            'contentMail'          => 'test mail {SANTA} => {TARGET}',
            'contentSMS'           => '',
            'dearsanta'            => '1',
            'dearsanta-expiration' => date('Y-m-d', strtotime('+2 days')),
        ], 200);
        $this->assertEquals(['message' => 'Envoyé avec succès !'], $content);

        // For security issues, the key is only sent by mail and never stored
        // So fetch it from the mail
        $link = null;
        Mail::assertSent(\Korko\SecretSanta\Mail\TargetDrawn::class, function($mail) use(&$link) {
            $link = $mail->dearSantaLink;

            return true;
        });
        $this->assertNotNull($link);

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
            'content'              => 'test dearsanta mail content'
        ], 200);
        $this->assertEquals(['message' => 'Envoyé avec succès !'], $content);

        Mail::assertSent(\Korko\SecretSanta\Mail\DearSanta::class);

    }
}
