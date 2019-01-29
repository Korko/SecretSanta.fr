<?php

namespace Tests\Feature;

use Mail;
use NoCaptcha;

class RequestOrganizerTest extends RequestCase
{
    use \Illuminate\Foundation\Testing\DatabaseMigrations;
    use \Illuminate\Foundation\Testing\DatabaseTransactions;

    public function testOrganizer()
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
        $content = $this->ajaxPost('/', [
            'g-recaptcha-response' => 'mocked',
            'participants'         => $participants,
            'title'                => 'test mail title',
            'contentMail'          => 'test mail {SANTA} => {TARGET}',
            'contentSMS'           => '',
            'dearsanta'            => '1',
            'data-expiration'      => date('Y-m-d', strtotime('+2 days')),
        ], 200);
        $this->assertEquals(['message' => 'Envoyé avec succès !'], $content);

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

        /*
                        // Try to contact santa
                        $content = $this->ajaxPost($path, [
                            'g-recaptcha-response' => 'mocked',
                            'key'                  => $key,
                            'title'                => 'test dearsanta mail title',
                            'content'              => 'test dearsanta mail content',
                        ], 200);
                        $this->assertEquals(['message' => 'Envoyé avec succès !'], $content);
        
                        Mail::assertSent(\App\Mail\DearSanta::class, function ($mail) use ($id, $participants) {
                            $santaId = array_search($id, array_column($participants, 'target'));
                            $santa = $participants[$santaId];
        
                            return $mail->hasTo($santa['email']);
                        });
        */
    }
}
