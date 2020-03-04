<?php

namespace Tests\Feature;

use App\Draw;
use App\Mail\OrganizerRecap;
use App\Mail\TargetDrawn;
use App\Participant;
use Mail;
use Metrics;

class RequestTest extends RequestCase
{
    use \Illuminate\Foundation\Testing\DatabaseMigrations;
    use \Illuminate\Foundation\Testing\DatabaseTransactions;

    protected function validateForm($parameters)
    {
        return $this->ajaxPost('/', array_merge([
            'data-expiration'      => date('Y-m-d', strtotime('+2 days')),
        ], $parameters));
    }

    public function testInvalid(): void
    {
        Mail::shouldReceive('to')
            ->never();

        Metrics::shouldReceive('increment')
            ->never();

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
                    'email'      => 'test3@test.com',
                    'exclusions' => ['0', '2'],
                ],
                [
                    'name'       => 'tutu',
                    'email'      => 'test2@test.com',
                    'exclusions' => ['1'],
                ],
            ],
            'title'                => 'test mail title',
            'content-email'        => 'test mail {SANTA} => {TARGET}',
        ]);

        $response
            ->assertStatus(500)
            ->assertJson([
                'error' => 'Aucune solution possible',
            ]);

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
            ->times(3)
            ->with('email')
            ->andReturn(true);

        Mail::fake();
        Mail::assertNothingSent();

        // TODO: also test mail content

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
                    'email'      => 'test3@test.com',
                    'exclusions' => ['0'],
                ],
                [
                    'name'       => 'tutu',
                    'email'      => 'test2@test.com',
                    'exclusions' => ['1'],
                ],
            ],
            'title'                => 'test mail {SANTA} => {TARGET} title',
            'content-email'        => 'test mail {SANTA} => {TARGET} body',
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

        $this->assertEquals(1, Draw::count());
        $this->assertEquals(3, Participant::count());
    }
}
