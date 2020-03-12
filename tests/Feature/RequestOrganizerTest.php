<?php

namespace Tests\Feature;

use App\Draw;
use App\Participant;
use Crypt;
use Queue;

class RequestOrganizerTest extends RequestCase
{
    private static $key;

    public function testDraw(): Draw
    {
        Queue::fake();

        self::$key = Crypt::getKey();

        // Participants can only select one person, all the others will be excluded
        $participants = $this->formatParticipants([
            [
                'name'   => 'toto',
                'email'  => 'test@test.com',
                'target' => 1,
            ],
            [
                'name'   => 'tata',
                'email'  => 'test2@test.com',
                'target' => 2,
            ],
            [
                'name'   => 'tutu',
                'email'  => 'test3@test.com',
                'target' => 0,
            ],
        ]);

        $response = $this->ajaxPost('/', [
            'participants'         => $participants,
            'title'                => 'test mail title',
            'content-email'        => 'test mail {SANTA} => {TARGET}',
            'data-expiration'      => date('Y-m-d', strtotime('+2 days')),
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Envoyé avec succès !',
            ]);

        Queue::assertPushed(\App\Jobs\SendMail::class, function ($job) {
            return $job->getMailable() instanceof \App\Mail\OrganizerFinalRecap;
        });

        // So fetch it from the mail
        $link = null;
        Queue::assertPushed(\App\Jobs\SendMail::class, function ($job) use (&$link) {
            if ($job->getMailable() instanceof \App\Mail\OrganizerRecap) {
                $link = $job->getMailable()->panelLink;

                return true;
            }

            return false;
        });
        $this->assertNotNull($link);

        $path = parse_url($link, PHP_URL_PATH);

        // Get the form page (just to check http code)
        $response = $this->get($path);
        $this->assertEquals(200, $response->status(), $response->__toString());

        // Check data stored are decryptable
        $pathTheorical = parse_url(route('organizerPanel', ['draw' => '%d']), PHP_URL_PATH);
        $data = sscanf($path, $pathTheorical);
        $draw = Draw::find($data[0]);

        $this->assertNotEquals(0, $draw->participants->count());

        foreach ($draw->participants as &$participant) {
            $this->assertContains($participant->name, array_column($participants, 'name'));
            $this->assertContains($participant->address, array_column($participants, 'email'));
        }

        return $draw;
    }

    /**
     * @depends testDraw
     */
    public function testSendAgain(Draw $draw): void
    {
        Queue::fake();

        Crypt::setKey(self::$key);

        $participant = $draw->participants->first();

        // Check data can be changed
        $path = route('organizerPanel.resendEmail', [
            'draw' => $draw->id,
            'participant' => $participant,
        ]);
        $response = $this->ajaxPost($path, [
            'key' => base64_encode(Crypt::getKey()),
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Envoyé avec succès !',
            ]);

        Queue::assertPushed(\App\Jobs\SendMail::class, function ($job) use ($participant) {
            return $job->getMailable() instanceof \App\Mail\TargetDrawn &&
                   $job->getRecipient()->address === $participant->address;
        });
    }

    /**
     * @depends testDraw
     */
    public function testChangeEmail(Draw $draw): void
    {
        Queue::fake();

        Crypt::setKey(self::$key);

        $participant = $draw->participants->first();

        // Check data can be changed
        $path = route('organizerPanel.changeEmail', [
            'draw' => $draw->id,
            'participant' => $participant,
        ]);
        $response = $this->ajaxPost($path, [
            'key' => base64_encode(Crypt::getKey()),
            'email' => 'test@test2.com',
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Modifié avec succès !',
            ]);

        $before = $participant->address;

        $participant = Participant::find($participant->id);
        $after = $participant->address;

        $this->assertNotEquals($before, $after);
        $this->assertEquals('test@test2.com', $after);

        Queue::assertPushed(\App\Jobs\SendMail::class, function ($job) use ($participant) {
            return $job->getMailable() instanceof \App\Mail\TargetDrawn &&
                   $job->getRecipient()->address === $participant->address;
        });
    }
}
