<?php

namespace Tests\Feature;

use App\DearSanta;
use App\DearSantaDraw;
use App\MailBody;
use Config;
use Mail;
use Metrics;
use Mockery;
use NoCaptcha;

class RequestBounceTest extends RequestCase
{
    use \Illuminate\Foundation\Testing\DatabaseMigrations;

    /**
     * @group large
     */
    public function testBounce()
    {
        Config::set('mail.driver', 'sendgrid');

        NoCaptcha::shouldReceive('verifyResponse')->once()->andReturn(true);

        $this->assertEquals(0, DearSantaDraw::count());
        $this->assertEquals(0, DearSanta::count());

        $content = $this->ajaxPost('/', [
            'g-recaptcha-response' => 'mocked',
            'participants'         => [
                [
                    'name'       => 'toto',
                    'email'      => 'success@simulator.amazonses.com',
                    'phone'      => '',
                    'exclusions' => ['2'],
                ],
                [
                    'name'       => 'tata',
                    'email'      => 'bounce@simulator.amazonses.com',
                    'phone'      => '',
                    'exclusions' => ['0'],
                ],
                [
                    'name'       => 'tutu',
                    'email'      => 'bounce@simulator.amazonses.com',
                    'phone'      => '',
                    'exclusions' => ['1'],
                ],
            ],
            'title'                => 'test mail title',
            'contentMail'          => 'test mail {SANTA} => {TARGET}',
            'contentSMS'           => '',
            'dearsanta'            => '0',
        ], 200);
        $this->assertEquals(['message' => 'Envoyé avec succès !'], $content);

        $this->assertEquals(1, DearSantaDraw::count());
        $this->assertEquals(3, DearSanta::count());

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
                 'target'        => 'tutu',
                 'dearSantaLink' => null,
                 'mailBody'      => '1',
             ]),
             'email'         => 'test2@test.com',
             'event'         => 'dropped',
             'reason'        => 'Unsubscribed Address',
             'sg_event_id'   => 'wu3pyy4hQ4qo4TUGJ-E5KA',
             'sg_message_id' => 'QygG4vV9TL2uRMEPLh1mlg.filter0002p2iad2-29875-5C05903B-1D',
             'smtp-id'       => '<QygG4vV9TL2uRMEPLh1mlg@ismtpd0001p1lon1.sendgrid.net>',
             'timestamp'     => 1543868476,
        ], 200, true);

        $this->assertEquals(1, DearSantaDraw::count());
        $this->assertEquals(3, DearSanta::count());
    }
}
