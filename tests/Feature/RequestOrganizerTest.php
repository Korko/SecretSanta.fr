<?php

namespace Tests\Feature;

use App\Draw;
use App\Mail\OrganizerFinalRecap;
use App\Mail\OrganizerRecap;
use App\Mail\TargetDrawn;
use App\Participant;
use Crypt;
use Mail;

class RequestOrganizerTest extends RequestCase
{
    private static $key;

    public function testDraw(): Draw
    {
        Mail::fake();

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

        Mail::assertQueued(OrganizerFinalRecap::class);

        // So fetch it from the mail
        $link = null;
        Mail::assertQueued(OrganizerRecap::class, function ($mail) use (&$link) {
            $link = $mail->panelLink;

            return true;
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
        Mail::fake();

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

        Mail::assertQueued(TargetDrawn::class, function ($mail) use ($participant) {
            return $mail->hasTo($participant->address, $participant->name);
        });
    }

    /**
     * @depends testDraw
     */
    public function testChangeEmail(Draw $draw): void
    {
        Mail::fake();

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

        Mail::assertQueued(TargetDrawn::class, function ($mail) use ($participant) {
            return $mail->hasTo($participant->address, $participant->name);
        });
    }
}
