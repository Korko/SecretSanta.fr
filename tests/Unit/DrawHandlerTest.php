<?php

namespace Tests\Unit;

use App\Exceptions\SolverException;
use App\Jobs\SendMail;
use App\Mail\OrganizerRecap;
use App\Mail\TargetDrawn;
use App\Models\Draw;
use App\Models\Exclusion;
use App\Models\Participant;
use DrawHandler;
use Exception;
use Mail;
use Tests\TestCase;

class DrawHandlerTest extends TestCase
{
    use \Illuminate\Foundation\Testing\DatabaseMigrations;
    use \Illuminate\Foundation\Testing\DatabaseTransactions;

    public function testInvalidCase(): void
    {
        Mail::fake();
        Mail::assertNothingSent();

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

    public function testClassic(): void
    {
        Mail::fake();

        $this->assertEquals(0, Draw::count());
        $this->assertEquals(0, Participant::count());
        $this->assertEquals(0, Exclusion::count());

        $participants = [
            ['name' => uniqid(), 'email' => 'test@test.com', 'exclusions' => [2]],
            ['name' => uniqid(), 'email' => 'test3@test.com', 'exclusions' => [0]],
            ['name' => uniqid(), 'email' => 'test2@test.com', 'exclusions' => [1]],
        ];

        DrawHandler::toParticipants($participants)
            ->expiresAt(date('Y-m-d', strtotime('+2 days')))
            ->sendMail('test mail {SANTA} => {TARGET} title', 'test mail {SANTA} => {TARGET} body');

        // Ensure Organizer receives his recap
        $this->assertHasMailPushed(OrganizerRecap::class, 'test@test.com', function ($m) use (&$link) {
            $link = $m->panelLink;
        });

        // TODO: assert body
        // Ensure Participants receive their own recap
        $title = null;
        $this->assertHasMailPushed(TargetDrawn::class, 'test@test.com', function ($m) use (&$title) {
            $title = $m->subject;
        });
        $this->assertStringContainsString('test mail '.$participants[0]['name'].' => '.$participants[1]['name'].' title', html_entity_decode($title));
        $this->assertHasMailPushed(TargetDrawn::class, 'test2@test.com');

        $this->assertEquals(1, Draw::count());
        $this->assertEquals(3, Participant::count());
        $this->assertEquals(3, Exclusion::count());

        // Carreful, array is 0..n, Db is 1..n
        $this->assertEquals($participants[0]['name'], Participant::find(1)->name);
        $this->assertEquals(Participant::find(3)->id, Participant::find(1)->exclusions[0]->id);

        // Check data stored are decryptable
        $path = parse_url($link, PHP_URL_PATH);
        $pathTheorical = parse_url(route('organizerPanel', ['draw' => '%s']), PHP_URL_PATH);
        $data = sscanf($path, $pathTheorical);
        $draw = Draw::findByHashOrFail($data[0]);

        $this->assertNotEquals(0, $draw->participants->count());

        foreach ($draw->participants as $participant) {
            $this->assertContains($participant->name, array_column($participants, 'name'));
            $this->assertContains($participant->email, array_column($participants, 'email'));
        }
    }
}
