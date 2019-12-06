<?php

namespace Tests\Feature;

use Mail;
use Crypt;
use App\Draw;

class RequestOrganizerTest extends RequestCase
{
    public function testDraw(): Draw
    {
        Mail::fake();

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
            'g-recaptcha-response' => 'mocked',
            'participants'         => $participants,
            'title'                => 'test mail title',
            'content-email'        => 'test mail {SANTA} => {TARGET}',
            'dearsanta'            => '1',
            'data-expiration'      => date('Y-m-d', strtotime('+2 days')),
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Envoyé avec succès !',
            ]);

        // So fetch it from the mail
        $link = null;
        Mail::assertQueued(\App\Mail\OrganizerRecap::class, function ($mail) use (&$link) {
            $link = $mail->panelLink;

            return true;
        });
        $this->assertNotNull($link);

        $path = parse_url($link, PHP_URL_PATH);

        $key = base64_decode(parse_url($link, PHP_URL_FRAGMENT));
        Crypt::setKey($key);//TODO check needed?

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
            $this->assertContains($participant->email_address, array_column($participants, 'email'));
        }

        return $draw;
    }

    /**
     * @depends testDraw
     */
    public function testOrganizerPanel(Draw $draw): void
    {
        Mail::fake();

        $participant = $draw->participants->first();

        // Check data can be changed
        $path = route('organizerPanel.changeEmail', [
            'draw' => $draw->id,
            'participant' => $participant,
        ]);
        $response = $this->ajaxPost($path, [
            'g-recaptcha-response' => 'mocked',
            'key' => base64_encode(Crypt::getKey()),
            'email' => 'test@test2.com',
        ]);

//        $before = $participant->email_address;
//        $after = $participant->fresh()->shareEncryptionKey($draw)->email_address;
//        $this->assertNotEquals($before, $after);
//        $this->assertEquals('test@test2.com', $after);
        /*
                $response
                    ->assertStatus(200)
                    ->assertJson([
                        'message' => 'Envoyé avec succès !',
                    ]);*/
    }
}
