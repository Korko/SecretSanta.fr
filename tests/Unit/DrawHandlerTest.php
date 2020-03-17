<?php

namespace Tests\Unit;

use App\Draw;
use App\Exceptions\SolverException;
use App\Jobs\SendMail;
use App\Mail\OrganizerRecap;
use App\Mail\TargetDrawn;
use App\Participant;
use DrawHandler;
use Exception;
use Metrics;
use Queue;
use Tests\TestCase;

class DrawHandlerTest extends TestCase
{
    use \Illuminate\Foundation\Testing\DatabaseMigrations;
    use \Illuminate\Foundation\Testing\DatabaseTransactions;

    public function test_invalid_case(): void
    {
        Queue::fake();
        Queue::assertNotPushed(SendMail::class);

        Metrics::shouldReceive('increment')
            ->never();

        $this->assertEquals(0, Draw::count());
        $this->assertEquals(0, Participant::count());

        try {
            DrawHandler::toParticipants([
                ['name' => 'toto', 'email' => 'test@test.com', 'exclusions' => [2]],
                ['name' => 'tata', 'email' => 'test3@test.com', 'exclusions' => [0, 2]],
                ['name' => 'tutu', 'email' => 'test2@test.com', 'exclusions' => [1]],
            ]);

            $this->fail('Expected Exception');
        } catch (Exception $e) {
            $this->assertEquals(get_class($e), SolverException::class);
        }

        $this->assertEquals(0, Draw::count());
        $this->assertEquals(0, Participant::count());
    }

    public function test_classic(): void
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

        $this->assertEquals(0, Draw::count());
        $this->assertEquals(0, Participant::count());

        DrawHandler::toParticipants([
                ['name' => 'toto', 'email' => 'test@test.com', 'exclusions' => [2]],
                ['name' => 'tata', 'email' => 'test3@test.com', 'exclusions' => [0]],
                ['name' => 'tutu', 'email' => 'test2@test.com', 'exclusions' => [1]],
            ])
            ->expiresAt(date('Y-m-d', strtotime('+2 days')))
            ->sendMail('test mail {SANTA} => {TARGET} title', 'test mail {SANTA} => {TARGET} body');

        Queue::assertPushed(SendMail::class, function ($job) {
            return $job->getMailable() instanceof OrganizerRecap &&
                   $job->getRecipient()->address === 'test@test.com';
        });

        $title = $body = null;
        Queue::assertPushed(SendMail::class, function ($job) use (&$title, &$body) {
            if (
                $job->getMailable() instanceof TargetDrawn &&
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
                $job->getMailable() instanceof TargetDrawn &&
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
