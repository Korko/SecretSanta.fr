<?php

namespace Tests\Unit;

use Artisan;
use Korko\SecretSanta\Draw;
use Korko\SecretSanta\Participant;
use Mail;
use Metrics;
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

        Metrics::shouldReceive('increment')
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

        Metrics::shouldReceive('increment')
            ->once()
            ->with('draws')
            ->andReturn(true);

        Metrics::shouldReceive('increment')
            ->once()
            ->with('participants', 3)
            ->andReturn(true);

        Metrics::shouldReceive('increment')
            ->times(2)
            ->with('email')
            ->andReturn(true);

        Metrics::shouldReceive('increment')
            ->times(2)
            ->with('phone')
            ->andReturn(true);

        Metrics::shouldReceive('increment')
            ->times(2)
            ->with('sms', 1)
            ->andReturn(true);

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
            ->with('+33612345678', '#test sms "tata\' => &tutu#')
            ->andReturn(true);

        Sms::shouldReceive('message')
            ->once()
            ->with('+33712345678', '#test sms "tutu\' => &toto#')
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

    public function testLongSmsOnly()
    {
        Recaptcha::shouldReceive('verify')->once()->andReturn(true);

        Metrics::shouldReceive('increment')
            ->once()
            ->with('draws')
            ->andReturn(true);

        Metrics::shouldReceive('increment')
            ->once()
            ->with('participants', 3)
            ->andReturn(true);

        Metrics::shouldReceive('increment')
            ->never()
            ->with('email')
            ->andReturn(true);

        Metrics::shouldReceive('increment')
            ->times(3)
            ->with('phone')
            ->andReturn(true);

        Metrics::shouldReceive('increment')
            ->times(3)
            ->with('sms', 2)
            ->andReturn(true);

        Mail::shouldReceive('send')
            ->never();

        Sms::shouldReceive('message')
            ->once()
            ->with('+33612345678', '#test sms "toto\' => &tata#')
            ->andReturn(true);

        Sms::shouldReceive('message')
            ->once()
            ->with('+33612345679', '#test sms "tata\' => &tutu#')
            ->andReturn(true);

        Sms::shouldReceive('message')
            ->once()
            ->with('+33712345670', '#test sms "tutu\' => &toto#')
            ->andReturn(true);

        $this->assertEquals(0, Draw::count());
        $this->assertEquals(0, Participant::count());

        $content = $this->ajaxPost('/', [
            'g-recaptcha-response' => 'mocked',
            'name'                 => ['toto', 'tata', 'tutu'],
            'email'                => ['', '', ''],
            'phone'                => ['0612345678', '0612345679', '712345670'],
            'exclusions'           => [['2'], ['0'], ['1']],
            'title'                => 'test mail title',
            'contentMail'          => '',
            'contentSMS'           => 'test sms "{SANTA}\' => &{TARGET}'.implode('', array_fill(0, 160, 'a')),
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

        Metrics::shouldReceive('increment')
            ->once()
            ->with('draws')
            ->andReturn(true);

        Metrics::shouldReceive('increment')
            ->once()
            ->with('participants', 3)
            ->andReturn(true);

        Metrics::shouldReceive('increment')
            ->times(3)
            ->with('email')
            ->andReturn(true);

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
