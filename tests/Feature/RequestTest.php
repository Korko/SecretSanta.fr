<?php

namespace Tests\Feature;

use App\Draw;
use App\Jobs\SendMail;
use App\Participant;
use Metrics;
use Queue;

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
        Queue::fake();
        Queue::assertNotPushed(SendMail::class);

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

        Queue::fake();
        Queue::assertNotPushed(SendMail::class);

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

        Queue::assertPushed(SendMail::class, function ($job) {
            return $job->getMailable() instanceof \App\Mail\OrganizerRecap &&
                   $job->getRecipient()->address === 'test@test.com';
        });

        $title = $body = null;

        Queue::assertPushed(SendMail::class, function ($job) use (&$title, &$body) {
            if (
                $job->getMailable() instanceof \App\Mail\TargetDrawn &&
                $job->getRecipient()->address === 'test@test.com'
            ) {
                $title = $job->getMailable()->subject;
                //$m = $job->getMailable()->build();
                //$body = view($m->view, $m->buildViewData())->render();

                return true;
            }

            return false;
        });
        $this->assertStringContainsString('test mail toto => tata title', html_entity_decode($title));
        //$this->assertStringContainsString('test mail toto => tata body', html_entity_decode($body));

        $body = null;
        Queue::assertPushed(SendMail::class, function ($job) use (&$title, &$body) {
            if (
                $job->getMailable() instanceof \App\Mail\TargetDrawn &&
                $job->getRecipient()->address === 'test2@test.com'
            ) {
                //$m = $job->getMailable()->build();
                //$body = view($m->view, $m->buildViewData())->render();

                return true;
            }

            return false;
        });
        //$this->assertStringContainsString('test mail tutu => toto', html_entity_decode($body));

        $this->assertEquals(1, Draw::count());
        $this->assertEquals(3, Participant::count());
    }
}
