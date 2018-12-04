<?php

namespace Tests\Feature;

use App\Draw;
use App\MailBody;
use App\Participant;
use Artisan;
use Mail;
use Metrics;
use Mockery;
use NoCaptcha;
use Sms;

class RequestBounceTest extends RequestCase
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

    public function testBounce()
    {
        NoCaptcha::shouldReceive('verifyResponse')->once()->andReturn(true);

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
            ->with(\Mockery::type(\App\Mail\TargetDrawn::class))
            ->andReturn(Mockery::self());

        $this->assertEquals(0, Draw::count());
        $this->assertEquals(0, Participant::count());
        $this->assertEquals(0, MailBody::count());

        $content = $this->ajaxPost('/', [
            'g-recaptcha-response' => 'mocked',
            'name'                 => ['toto', 'tata', 'tutu'],
            'email'                => ['test@test.com', 'test2@test.com', 'test3@test.com'],
            'phone'                => ['', '', ''],
            'exclusions'           => [['2'], ['0'], ['1']],
            'title'                => 'test mail title',
            'contentMail'          => 'test mail {SANTA} => {TARGET}',
            'contentSMS'           => '',
            'dearsanta'            => '0',
        ], 200);
        $this->assertEquals(['message' => 'Envoyé avec succès !'], $content);

        $this->assertEquals(0, Draw::count());
        $this->assertEquals(0, Participant::count());
        $this->assertEquals(1, MailBody::count());

        // Simulate a bounce, note which mail should be sent
        Mail::shouldReceive('to')
            ->once()
            ->with('test@test.com', 'toto')
            ->andReturn(Mockery::self());

        Mail::shouldReceive('send')
            ->once()
            ->with(\Mockery::type(\App\Mail\MailBounced::class))
            ->andReturn(Mockery::self());

        Metrics::shouldReceive('increment')
            ->once()
            ->with('email_bounced')
            ->andReturn(true);

        Metrics::shouldReceive('increment')
            ->once()
            ->with('email')
            ->andReturn(true);

        $content = $this->ajaxPost('/event', [
             'data' => json_encode([
                 'organizer' => [
                     'email' => 'test@test.com',
                     'name'  => 'toto',
                 ],
                 'target' => 'tutu',
                 'dearSantaLink' => null,
                 'mailBody' => '1',
             ]),
             'email' => 'test2@test.com',
             'event' => 'dropped',
             'reason' => 'Unsubscribed Address',
             'sg_event_id' => 'wu3pyy4hQ4qo4TUGJ-E5KA',
             'sg_message_id' => 'QygG4vV9TL2uRMEPLh1mlg.filter0002p2iad2-29875-5C05903B-1D',
             'smtp-id' => '<QygG4vV9TL2uRMEPLh1mlg@ismtpd0001p1lon1.sendgrid.net>',
             'timestamp' => 1543868476,
        ], 200, true);

        // No change
        $this->assertEquals(0, Draw::count());
        $this->assertEquals(0, Participant::count());
        $this->assertEquals(1, MailBody::count());
    }
}
