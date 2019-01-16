<?php

namespace Tests\Feature;

use App\DearSanta;
use App\Draw;
use App\Mail\Organizer as OrganizerEmail;
use App\Mail\TargetDrawn;
use App\Participant;
use Mail;
use Metrics;
use NoCaptcha;
use Sms;

class RequestTest extends RequestCase
{
    use \Illuminate\Foundation\Testing\DatabaseMigrations;
    use \Illuminate\Foundation\Testing\DatabaseTransactions;

    protected function validateForm($parameters, $httpCode)
    {
        NoCaptcha::shouldReceive('verifyResponse')->once()->andReturn(true);

        return $this->ajaxPost('/', array_merge([
            'g-recaptcha-response' => 'mocked',
            'dearsanta'            => '0',
            'data-expiration'      => date('Y-m-d', strtotime('+2 days')),
        ], $parameters), $httpCode);
    }

    public function testInvalid()
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

        $content = $this->validateForm([
            'participants'         => [
                [
                    'name'       => 'toto',
                    'email'      => 'test@test.com',
                    'phone'      => '',
                    'exclusions' => ['2'],
                ],
                [
                    'name'       => 'tata',
                    'email'      => '',
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
            'contentMail'          => 'test mail {SANTA} => {TARGET}',
            'contentSMS'           => 'test sms "{SANTA}\' => &{TARGET}',
        ], 500);
        $this->assertEquals(['error' => 'Aucune solution possible'], $content);

        $this->assertEquals(0, DearSanta::count());
        $this->assertEquals(0, Draw::count());
        $this->assertEquals(0, Participant::count());
    }

    public function testClassic()
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

        $content = $this->validateForm([
            'participants'         => [
                [
                    'name'       => 'toto',
                    'email'      => 'test@test.com',
                    'phone'      => '',
                    'exclusions' => ['2'],
                ],
                [
                    'name'       => 'tata',
                    'email'      => '',
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
            'title'                => 'test mail title',
            'contentMail'          => 'test mail {SANTA} => {TARGET}',
            'contentSMS'           => 'test sms "{SANTA}\' => &{TARGET}',
        ], 200);
        $this->assertEquals(['message' => 'Envoyé avec succès !'], $content);

        Mail::assertSent(OrganizerEmail::class, function ($mail) {
            return $mail->hasTo('test@test.com', 'toto');
        });

        Mail::assertSent(TargetDrawn::class, function ($mail) {
            return $mail->hasTo('test@test.com', 'toto');
        });

        Mail::assertSent(TargetDrawn::class, function ($mail) {
            return $mail->hasTo('test2@test.com', 'tutu');
        });

        $this->assertEquals(0, DearSanta::count());
        $this->assertEquals(1, Draw::count());
        $this->assertEquals(3, Participant::count());
    }

    public function testLongSmsOnly()
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

        $content = $this->validateForm([
            'participants'         => [
                [
                    'name'       => 'toto',
                    'email'      => 'test@test.com',
                    'phone'      => '0612345678',
                    'exclusions' => ['2'],
                ],
                [
                    'name'       => 'tata',
                    'email'      => '',
                    'phone'      => '0612345679',
                    'exclusions' => ['0'],
                ],
                [
                    'name'       => 'tutu',
                    'email'      => '',
                    'phone'      => '712345670',
                    'exclusions' => ['1'],
                ],
            ],
            'title'                => 'test mail title',
            'contentMail'          => 'test mail {SANTA} => {TARGET}',
            'contentSMS'           => 'test sms "{SANTA}\' => &{TARGET}'.implode('', array_fill(0, 160, 'a')),
        ], 200);
        $this->assertEquals(['message' => 'Envoyé avec succès !'], $content);

        Mail::assertSent(OrganizerEmail::class, function ($mail) {
            return $mail->hasTo('test@test.com', 'toto');
        });

        Mail::assertSent(TargetDrawn::class, function ($mail) {
            return $mail->hasTo('test@test.com', 'toto');
        });

        $this->assertEquals(0, DearSanta::count());
        $this->assertEquals(1, Draw::count());
        $this->assertEquals(3, Participant::count());
    }

    public function testDearsanta()
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

        $content = $this->validateForm([
            'participants'         => [
                [
                    'name'       => 'toto',
                    'email'      => 'test@test.com',
                    'phone'      => '',
                    'exclusions' => ['2'],
                ],
                [
                    'name'       => 'tata',
                    'email'      => 'test2@test.com',
                    'phone'      => '',
                ],
                [
                    'name'       => 'tutu',
                    'email'      => 'test3@test.com',
                    'phone'      => '',
                ],
            ],
            'title'                => 'test mail title',
            'contentMail'          => 'test mail {SANTA} => {TARGET}',
            'contentSMS'           => 'test sms "{SANTA}\' => &{TARGET}',
            'dearsanta'            => '1',
        ], 200);
        $this->assertEquals(['message' => 'Envoyé avec succès !'], $content);

        Mail::assertSent(OrganizerEmail::class, function ($mail) {
            return $mail->hasTo('test@test.com', 'toto');
        });

        Mail::assertSent(TargetDrawn::class, function ($mail) {
            return $mail->hasTo('test@test.com', 'toto');
        });

        Mail::assertSent(TargetDrawn::class, function ($mail) {
            return $mail->hasTo('test2@test.com', 'tata');
        });

        Mail::assertSent(TargetDrawn::class, function ($mail) {
            return $mail->hasTo('test3@test.com', 'tutu');
        });

        $this->assertEquals(3, DearSanta::count());
        $this->assertEquals(1, Draw::count());
        $this->assertEquals(3, Participant::count());
    }
}
