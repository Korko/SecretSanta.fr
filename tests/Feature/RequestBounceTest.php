<?php

namespace Tests\Feature;

use Mail;
use Config;
use Metrics;
use Mockery;
use App\Draw;
use NoCaptcha;
use App\DearSanta;
use App\Participant;

class RequestBounceTest extends RequestCase
{
    use \Illuminate\Foundation\Testing\DatabaseMigrations;
    use \Illuminate\Foundation\Testing\DatabaseTransactions;

    /**
     * @group large
     */
    public function testBounce(): void
    {
        Config::set('mail.driver', 'smtp');

        NoCaptcha::shouldReceive('verifyResponse')->once()->andReturn(true);

        $this->assertEquals(0, Draw::count());
        $this->assertEquals(0, Participant::count());
        $this->assertEquals(0, DearSanta::count());

        $response = $this->ajaxPost('/', [
            'g-recaptcha-response' => 'mocked',
            'participants'         => [
                [
                    'name'       => 'toto',
                    'email'      => 'success@simulator.amazonses.com',
                    'exclusions' => ['2'],
                ],
                [
                    'name'       => 'tata',
                    'email'      => 'bounce@simulator.amazonses.com',
                    'exclusions' => ['0'],
                ],
                [
                    'name'       => 'tutu',
                    'email'      => 'bounce@simulator.amazonses.com',
                    'exclusions' => ['1'],
                ],
            ],
            'title'                => 'test mail title',
            'content-email'        => 'test mail {SANTA} => {TARGET}',
            'dearsanta'            => '0',
            'data-expiration'      => date('Y-m-d', strtotime('+2 days')),
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Envoyé avec succès !',
            ]);

        $this->assertEquals(1, Draw::count());
        $this->assertEquals(3, Participant::count());
        $this->assertEquals(0, DearSanta::count());

        /*
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
        */
        $response = $this->rawAjaxPost('/event', [
             'data'          => '{}',
             'email'         => 'test2@test.com',
             'event'         => 'dropped',
             'reason'        => 'Unsubscribed Address',
             'sg_event_id'   => 'wu3pyy4hQ4qo4TUGJ-E5KA',
             'sg_message_id' => 'QygG4vV9TL2uRMEPLh1mlg.filter0002p2iad2-29875-5C05903B-1D',
             'smtp-id'       => '<QygG4vV9TL2uRMEPLh1mlg@ismtpd0001p1lon1.sendgrid.net>',
             'timestamp'     => 1543868476,
        ]);

        $response->assertStatus(200);

        $this->assertEquals(1, Draw::count());
        $this->assertEquals(3, Participant::count());
        $this->assertEquals(0, DearSanta::count());
    }
}
