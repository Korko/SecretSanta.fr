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

    public function testClassic()
    {
        Recaptcha::shouldReceive('verify')->once()->andReturn(true);

        Mail::shouldReceive('raw')
            ->once()
            ->with('#test mail toto => tata#', Mockery::on(function ($closure) {
                $message = Mockery::mock('Illuminate\Mailer\Message');
                $message->shouldReceive('to')
                    ->with('test@test.com', 'toto')
                    ->andReturn(Mockery::self());
                $message->shouldReceive('subject')
                    ->with('test mail title')
                    ->andReturn(Mockery::self());
                $closure($message);

                return true;
            }))
            ->andReturn(true);

        Mail::shouldReceive('raw')
            ->once()
            ->with('#test mail tutu => toto#', Mockery::on(function ($closure) {
                $message = Mockery::mock('Illuminate\Mailer\Message');
                $message->shouldReceive('to')
                    ->with('test2@test.com', 'tutu')
                    ->andReturn(Mockery::self());
                $message->shouldReceive('subject')
                    ->with('test mail title')
                    ->andReturn(Mockery::self());
                $closure($message);

                return true;
            }))
            ->andReturn(true);

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
        $this->assertEquals(['Envoyé avec succès !'], $content);

        $this->assertEquals(0, Draw::count());
        $this->assertEquals(0, Participant::count());
    }

    public function testDearsanta()
    {
        Recaptcha::shouldReceive('verify')->once()->andReturn(true);

        Mail::shouldReceive('raw')
            ->once()
            ->with('#test mail toto => tata.+/dearsanta/[0-9]+\#[a-f0-9]+#s', Mockery::on(function ($closure) {
                $message = Mockery::mock('Illuminate\Mailer\Message');
                $message->shouldReceive('to')
                    ->with('test@test.com', 'toto')
                    ->andReturn(Mockery::self());
                $message->shouldReceive('subject')
                    ->with('test mail title')
                    ->andReturn(Mockery::self());
                $closure($message);

                return true;
            }))
            ->andReturn(true);

        Mail::shouldReceive('raw')
            ->once()
            ->with('#test mail tata => tutu.+/dearsanta/[0-9]+\#[a-f0-9]+#s', Mockery::on(function ($closure) {
                $message = Mockery::mock('Illuminate\Mailer\Message');
                $message->shouldReceive('to')
                    ->with('test2@test.com', 'tata')
                    ->andReturn(Mockery::self());
                $message->shouldReceive('subject')
                    ->with('test mail title')
                    ->andReturn(Mockery::self());
                $closure($message);

                return true;
            }))
            ->andReturn(true);

        Mail::shouldReceive('raw')
            ->once()
            ->with('#test mail tutu => toto.+/dearsanta/[0-9]+\#[a-f0-9]+#s', Mockery::on(function ($closure) {
                $message = Mockery::mock('Illuminate\Mailer\Message');
                $message->shouldReceive('to')
                    ->with('test3@test.com', 'tutu')
                    ->andReturn(Mockery::self());
                $message->shouldReceive('subject')
                    ->with('test mail title')
                    ->andReturn(Mockery::self());
                $closure($message);

                return true;
            }))
            ->andReturn(true);

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
            'dearsanta-limit'      => date('Y-m-d', strtotime('+2 days')),
        ], 200);
        $this->assertEquals(['Envoyé avec succès !'], $content);

        $this->assertEquals(1, Draw::count());
        $this->assertEquals(3, Participant::count());
    }
}
