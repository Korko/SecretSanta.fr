<?php

namespace Tests\Feature;

use Mail;
use App\Draw;
use NoCaptcha;

class RequestOrganizerTest extends RequestCase
{
    use \Illuminate\Foundation\Testing\DatabaseMigrations;
    use \Illuminate\Foundation\Testing\DatabaseTransactions;

    public function testOrganizer(): void
    {
        Mail::fake();
        NoCaptcha::shouldReceive('verifyResponse')->andReturn(true);
        NoCaptcha::makePartial(); // We don't want to mock the display

        // Participants can only select one person, all the others will be excluded
        $participants = [
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
        ];

        $participants = array_map(function ($id) use ($participants) {
            return $participants[$id] + [
                'exclusions' => array_values(array_map('strval', array_diff(array_keys($participants), [$id], [$participants[$id]['target']]))),
            ];
        }, array_keys($participants));

        // Initiate DearSanta
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
        Mail::assertSent(\App\Mail\OrganizerRecap::class, function ($mail) use (&$link) {
            $link = $mail->panelLink;

            return true;
        });
        $this->assertNotNull($link);

        $path = parse_url($link, PHP_URL_PATH);
        $key = base64_decode(parse_url($link, PHP_URL_FRAGMENT));

        // Get the form page (just to check http code)
        $response = $this->get($path);
        $this->assertEquals(200, $response->status(), $response->__toString());

        // Check data stored are decryptable
        $pathTheorical = parse_url(route('organizerPanel', ['draw' => '%d']), PHP_URL_PATH);
        $data = sscanf($path, $pathTheorical);
        $draw = Draw::find($data[0]);

        foreach ($draw->participants as $participant) {
            $participant->setEncryptionKey($key);

            $this->assertContains($participant->name, array_column($participants, 'name'));
            $this->assertContains($participant->email_address, array_column($participants, 'email'));
        }

        // Check data can be changed
        $response = $this->ajaxPost($path, [
            'g-recaptcha-response' => 'mocked',
            ''
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Envoy   avec succ  s !',
            ]);
    }
}
