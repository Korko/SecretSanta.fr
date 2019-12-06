<?php

namespace Tests\Feature;

use Sms;
use Mail;
use Metrics;
use App\Draw;
use App\DearSanta;
use App\Participant;
use App\Mail\TargetDrawn;
use App\Mail\OrganizerRecap;

class RequestTest extends RequestCase
{
    use \Illuminate\Foundation\Testing\DatabaseMigrations;
    use \Illuminate\Foundation\Testing\DatabaseTransactions;

    protected function validateForm($parameters)
    {
        return $this->ajaxPost('/', array_merge([
            'g-recaptcha-response' => 'mocked',
            'dearsanta'            => '0',
            'data-expiration'      => date('Y-m-d', strtotime('+2 days')),
        ], $parameters));
    }

    public function testInvalid(): void
    {
        Mail::shouldReceive('to')
            ->never();

        Sms::shouldReceive('message')
            ->never();

        Metrics::shouldReceive('increment')
            ->never();

        $this->assertEquals(0, DearSanta::count());
        $this->assertEquals(0, Draw::count());
        $this->assertEquals(0, Participant::count());

        $response = $this->validateForm([
            'participants'         => [
                [
                    'name'       => 'toto',
                    'email'      => 'test@test.com',
                    'exclusions' => ['2'],
                ],
                [
                    'name'       => 'tata',
                    'phone'      => '0612345678',
                    'exclusions' => ['0', '2'],
                ],
                [
                    'name'       => 'tutu',
                    'email'      => 'test2@test.com',
                    'phone'      => '712345678',
                    'exclusions' => ['1'],
                ],
            ],
            'title'                => 'test mail title',
            'content-email'        => 'test mail {SANTA} => {TARGET}',
            'content-sms'          => 'test sms "{SANTA}\' => &{TARGET}',
        ]);

        $response
            ->assertStatus(500)
            ->assertJson([
                'error' => 'Aucune solution possible',
            ]);

        $this->assertEquals(0, DearSanta::count());
        $this->assertEquals(0, Draw::count());
        $this->assertEquals(0, Participant::count());
    }

    public function testClassic(): void
    {
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

        Mail::fake();
        Mail::assertNothingSent();

        // TODO: also test mail content

        Sms::shouldReceive('message')
            ->once()
            ->with('+33612345678', 'test sms "tata\' => &tutu')
            ->andReturn(true);

        Sms::shouldReceive('message')
            ->once()
            ->with('+33712345678', 'test sms "tutu\' => &toto')
            ->andReturn(true);

        $this->assertEquals(0, DearSanta::count());
        $this->assertEquals(0, Draw::count());
        $this->assertEquals(0, Participant::count());

        $response = $this->validateForm([
            'participants'         => [
                [
                    'name'       => 'toto',
                    'email'      => 'test@test.com',
                    'exclusions' => ['2'],
                ],
                [
                    'name'       => 'tata',
                    'phone'      => '0612345678',
                    'exclusions' => ['0'],
                ],
                [
                    'name'       => 'tutu',
                    'email'      => 'test2@test.com',
                    'phone'      => '712345678',
                    'exclusions' => ['1'],
                ],
            ],
            'title'                => 'test mail {SANTA} => {TARGET} title',
            'content-email'        => 'test mail {SANTA} => {TARGET} body',
            'content-sms'          => 'test sms "{SANTA}\' => &{TARGET}',
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Envoyé avec succès !',
            ]);

        Mail::assertQueued(OrganizerRecap::class, function ($mail) {
            return $mail->hasTo('test@test.com', 'toto');
        });

        $title = $body = null;
        Mail::assertQueued(TargetDrawn::class, function ($mail) use (&$title, &$body) {
            if ($mail->hasTo('test@test.com', 'toto')) {
                $m = $mail->build();
                $title = $mail->subject;
                //$body = view($m->view, $m->buildViewData())->render();

                return true;
            }
        });
        $this->assertStringContainsString('test mail toto => tata title', html_entity_decode($title));
        //$this->assertStringContainsString('test mail toto => tata body', html_entity_decode($body));

        $body = null;
        Mail::assertQueued(TargetDrawn::class, function ($mail) use (&$body) {
            if ($mail->hasTo('test2@test.com', 'tutu')) {
                $m = $mail->build();
                //$body = view($m->view, $m->buildViewData())->render();

                return true;
            }
        });
        //$this->assertStringContainsString('test mail tutu => toto', html_entity_decode($body));

        $this->assertEquals(0, DearSanta::count());
        $this->assertEquals(1, Draw::count());
        $this->assertEquals(3, Participant::count());
    }

    public function testLongSmsOnly(): void
    {
        Metrics::shouldReceive('increment')
            ->once()
            ->with('draws')
            ->andReturn(true);

        Metrics::shouldReceive('increment')
            ->once()
            ->with('participants', 3)
            ->andReturn(true);

        Metrics::shouldReceive('increment')
            ->once()
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

        Mail::fake();
        Mail::assertNothingSent();

        Sms::shouldReceive('message')
            ->once()
            ->with('+33612345678', \Mockery::pattern('#test sms "toto\' => &tata#'))
            ->andReturn(true);

        Sms::shouldReceive('message')
            ->once()
            ->with('+33612345679', \Mockery::pattern('#test sms "tata\' => &tutu#'))
            ->andReturn(true);

        Sms::shouldReceive('message')
            ->once()
            ->with('+33712345670', \Mockery::pattern('#test sms "tutu\' => &toto#'))
            ->andReturn(true);

        $this->assertEquals(0, DearSanta::count());
        $this->assertEquals(0, Draw::count());
        $this->assertEquals(0, Participant::count());

        $response = $this->validateForm([
            'participants'         => [
                [
                    'name'       => 'toto',
                    'email'      => 'test@test.com',
                    'phone'      => '0612345678',
                    'exclusions' => ['2'],
                ],
                [
                    'name'       => 'tata',
                    'phone'      => '0612345679',
                    'exclusions' => ['0'],
                ],
                [
                    'name'       => 'tutu',
                    'phone'      => '712345670',
                    'exclusions' => ['1'],
                ],
            ],
            'title'                => 'test mail title',
            'content-email'        => 'test mail {SANTA} => {TARGET}',
            'content-sms'          => 'test sms "{SANTA}\' => &{TARGET}'.implode('', array_fill(0, 160, 'a')),
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Envoyé avec succès !',
            ]);

        Mail::assertQueued(OrganizerRecap::class, function ($mail) {
            return $mail->hasTo('test@test.com', 'toto');
        });

        Mail::assertQueued(TargetDrawn::class, function ($mail) {
            return $mail->hasTo('test@test.com', 'toto');
        });

        $this->assertEquals(0, DearSanta::count());
        $this->assertEquals(1, Draw::count());
        $this->assertEquals(3, Participant::count());
    }

    public function testDearsanta(): void
    {
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

        Mail::fake();
        Mail::assertNothingSent();

        Sms::shouldReceive('message')
            ->never();

        $this->assertEquals(0, DearSanta::count());
        $this->assertEquals(0, Draw::count());
        $this->assertEquals(0, Participant::count());

        $response = $this->validateForm([
            'participants'         => [
                [
                    'name'       => 'toto',
                    'email'      => 'test@test.com',
                    'exclusions' => ['2'],
                ],
                [
                    'name'       => 'tata',
                    'email'      => 'test2@test.com',
                ],
                [
                    'name'       => 'tutu',
                    'email'      => 'test3@test.com',
                ],
            ],
            'title'                => 'test mail title',
            'content-email'        => 'test mail {SANTA} => {TARGET}',
            'content-sms'          => 'test sms "{SANTA}\' => &{TARGET}',
            'dearsanta'            => '1',
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Envoyé avec succès !',
            ]);

        Mail::assertQueued(OrganizerRecap::class, function ($mail) {
            return $mail->hasTo('test@test.com', 'toto');
        });

        Mail::assertQueued(TargetDrawn::class, function ($mail) {
            return $mail->hasTo('test@test.com', 'toto');
        });

        Mail::assertQueued(TargetDrawn::class, function ($mail) {
            return $mail->hasTo('test2@test.com', 'tata');
        });

        Mail::assertQueued(TargetDrawn::class, function ($mail) {
            return $mail->hasTo('test3@test.com', 'tutu');
        });

        $this->assertEquals(3, DearSanta::count());
        $this->assertEquals(1, Draw::count());
        $this->assertEquals(3, Participant::count());
    }
}
