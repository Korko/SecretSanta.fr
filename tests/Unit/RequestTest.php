<?php

namespace Tests\Unit;

use Artisan;
use Korko\SecretSanta\Draw;
use Korko\SecretSanta\Participant;
use Mail;
use Mockery;
use Recaptcha;
use Sms;

class RequestTest extends RequestCase
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

    public function testInvalid()
    {
        Recaptcha::shouldReceive('verify')->once()->andReturn(true);

        Mail::shouldReceive('to')
            ->never();

        Sms::shouldReceive('message')
            ->never();

        $this->assertEquals(0, Draw::count());
        $this->assertEquals(0, Participant::count());

        $content = $this->ajaxPost('/', [
            'g-recaptcha-response' => 'mocked',
            'name'                 => ['toto', 'tata', 'tutu'],
            'email'                => ['test@test.com', '', 'test2@test.com'],
            'phone'                => ['', '0612345678', '712345678'],
            'exclusions'           => [['2'], ['0', '2'], ['1']],
            'title'                => 'test mail title',
            'contentMail'          => 'test mail {SANTA} => {TARGET}',
            'contentSMS'           => 'test sms "{SANTA}\' => &{TARGET}',
            'dearsanta'            => '0',
        ], 500);
        $this->assertEquals(['error' => 'Aucune solution possible'], $content);

        $this->assertEquals(0, Draw::count());
        $this->assertEquals(0, Participant::count());
    }

    public function testClassic()
    {
        Recaptcha::shouldReceive('verify')->once()->andReturn(true);

        Mail::shouldReceive('to')
            ->once()
            ->with('test@test.com', 'toto')
            ->andReturn(Mockery::self());

        Mail::shouldReceive('to')
            ->once()
            ->with('test2@test.com', 'tutu')
            ->andReturn(Mockery::self());

        Mail::shouldReceive('send')
            ->times(2)
            ->with(\Mockery::type(\Korko\SecretSanta\Mail\TargetDrawn::class))
            ->andReturn(Mockery::self());

        // TODO: also test mail content

        Sms::shouldReceive('message')
            ->once()
            ->with('0612345678', '#test sms "tata\' => &tutu#')
            ->andReturn(true);

        Sms::shouldReceive('message')
            ->once()
            ->with('0712345678', '#test sms "tutu\' => &toto#')
            ->andReturn(true);

        $this->assertEquals(0, Draw::count());
        $this->assertEquals(0, Participant::count());

        $content = $this->ajaxPost('/', [
            'g-recaptcha-response' => 'mocked',
            'name'                 => ['toto', 'tata', 'tutu'],
            'email'                => ['test@test.com', '', 'test2@test.com'],
            'phone'                => ['', '0612345678', '712345678'],
            'exclusions'           => [['2'], ['0'], ['1']],
            'title'                => 'test mail title',
            'contentMail'          => 'test mail {SANTA} => {TARGET}',
            'contentSMS'           => 'test sms "{SANTA}\' => &{TARGET}',
            'dearsanta'            => '0',
        ], 200);
        $this->assertEquals(['message' => 'Envoyé avec succès !'], $content);

        // Nothing, no record, no dearsanta
        $this->assertEquals(0, Draw::count());
        $this->assertEquals(0, Participant::count());
    }

    public function testDearsanta()
    {
        Recaptcha::shouldReceive('verify')->once()->andReturn(true);

        Mail::shouldReceive('to')
            ->once()
            ->with('test@test.com', 'toto')
            ->andReturn(Mockery::self());

        Mail::shouldReceive('to')
            ->once()
            ->with('test2@test.com', 'tata')
            ->andReturn(Mockery::self());

        Mail::shouldReceive('to')
            ->once()
            ->with('test3@test.com', 'tutu')
            ->andReturn(Mockery::self());

        Mail::shouldReceive('send')
            ->times(3)
            ->with(\Mockery::type(\Korko\SecretSanta\Mail\TargetDrawn::class))
            ->andReturn(Mockery::self());

        // TODO: also test mail content

        Sms::shouldReceive('message')
            ->never();

        $this->assertEquals(0, Draw::count());
        $this->assertEquals(0, Participant::count());

        $content = $this->ajaxPost('/', [
            'g-recaptcha-response' => 'mocked',
            'name'                 => ['toto', 'tata', 'tutu'],
            'email'                => ['test@test.com', 'test2@test.com', 'test3@test.com'],
            'phone'                => ['', '', ''],
            'exclusions'           => [['2']],
            'title'                => 'test mail title',
            'contentMail'          => 'test mail {SANTA} => {TARGET}',
            'contentSMS'           => 'test sms "{SANTA}\' => &{TARGET}',
            'dearsanta'            => '1',
            'dearsanta-expiration' => date('Y-m-d', strtotime('+2 days')),
        ], 200);
        $this->assertEquals(['message' => 'Envoyé avec succès !'], $content);

        $this->assertEquals(1, Draw::count());
        $this->assertEquals(3, Participant::count());
    }
}
