<?php

namespace Tests\Feature;

use App\Draw;
use App\Services\SymmetricalEncrypter;
use Mail;
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
            'contentMail'          => 'test mail {SANTA} => {TARGET}',
            'contentSMS'           => '',
            'dearsanta'            => '1',
            'data-expiration'      => date('Y-m-d', strtotime('+2 days')),
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Envoyé avec succès !'
            ]);

        // So fetch it from the mail
        $link = null;
        Mail::assertSent(\App\Mail\Organizer::class, function ($mail) use (&$link) {
            $link = $mail->panelLink;

            return true;
        });
        $this->assertNotNull($link);

        $path = parse_url($link, PHP_URL_PATH);
        $key = parse_url($link, PHP_URL_FRAGMENT);

        // Get the form page (just to check http code)
        $response = $this->get($path);
        $this->assertEquals(200, $response->status(), $response->__toString());

        // Check data stored are decryptable
        $pathTheorical = parse_url(route('organizerPanel', ['santa' => '%d']), PHP_URL_PATH);
        $data = sscanf($path, $pathTheorical);
        $draw = Draw::find($data[0]);

        $encrypter = new SymmetricalEncrypter(base64_decode($key));

        foreach ($draw->participants as $participant) {
            $this->assertContains($encrypter->decrypt($participant->name, false), array_column($participants, 'name'));
            $this->assertContains($encrypter->decrypt($participant->email_address, false), array_column($participants, 'email'));
        }
    }
}
