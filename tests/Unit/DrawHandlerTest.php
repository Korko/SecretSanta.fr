<?php

namespace Tests\Unit;

use App\Draw;
use App\Exceptions\SolverException;
use App\Jobs\SendMail;
use App\Mail\OrganizerFinalRecap;
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

        $participants = [
            ['name' => 'toto', 'email' => 'test@test.com', 'exclusions' => [2]],
            ['name' => 'tata', 'email' => 'test3@test.com', 'exclusions' => [0]],
            ['name' => 'tutu', 'email' => 'test2@test.com', 'exclusions' => [1]],
        ];

        DrawHandler::toParticipants($participants)
            ->expiresAt(date('Y-m-d', strtotime('+2 days')))
            ->sendMail('test mail {SANTA} => {TARGET} title', 'test mail {SANTA} => {TARGET} body');

        // Ensure Organizer receives his recap
        $this->assertQueueHasMailPushed(OrganizerRecap::class, 'test@test.com', function ($m) use (&$link) {
            $link = $m->panelLink;
        });
        $this->assertQueueHasMailPushed(OrganizerFinalRecap::class, 'test@test.com');

        // TODO: assert body
        // Ensure Participants receive their own recap
        $title = null;
        $this->assertQueueHasMailPushed(TargetDrawn::class, 'test@test.com', function ($m) use (&$title, &$body) {
            $title = $m->subject;
        });
        $this->assertStringContainsString('test mail toto => tata title', html_entity_decode($title));
        $this->assertQueueHasMailPushed(TargetDrawn::class, 'test2@test.com');

        $this->assertEquals(1, Draw::count());
        $this->assertEquals(3, Participant::count());

        // Check data stored are decryptable
        $path = parse_url($link, PHP_URL_PATH);
        $pathTheorical = parse_url(route('organizerPanel', ['draw' => '%d']), PHP_URL_PATH);
        $data = sscanf($path, $pathTheorical);
        $draw = Draw::find($data[0]);

        $this->assertNotEquals(0, $draw->participants->count());

        foreach ($draw->participants as $participant) {
            $this->assertContains($participant->name, array_column($participants, 'name'));
            $this->assertContains($participant->email, array_column($participants, 'email'));
        }
    }
}
