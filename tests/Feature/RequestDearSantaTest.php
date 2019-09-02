<?php

namespace Tests\Feature;

use App\DearSanta;
use App\Services\SymmetricalEncrypter;
use Hashids;
use Mail;
use NoCaptcha;

class RequestDearSantaTest extends RequestCase
{
    use \Illuminate\Foundation\Testing\DatabaseMigrations;
    use \Illuminate\Foundation\Testing\DatabaseTransactions;

    public function testDearsanta(): void
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
                'message' => 'Envoyé avec succès !'
            ]);

        // For security issues, the key is only sent by mail and never stored
        // So fetch it from the mail
        $links = [];
        Mail::assertSent(\App\Mail\TargetDrawn::class, function ($mail) use (&$links) {
            $links[] = $mail->dearSantaLink;

            return true;
        });
        $this->assertEquals(count($participants), count($links));

        foreach ($links as $id => $link) {
            $path = parse_url($link, PHP_URL_PATH);
            $key = parse_url($link, PHP_URL_FRAGMENT);

            // Get the form page (just to check http code)
            $response = $this->get($path);
            $this->assertEquals(200, $response->status(), $response->__toString());

            $santaId = array_search($id, array_column($participants, 'target'));
            $santa = $participants[$santaId];

            // Check data stored are decryptable
            $pathTheorical = parse_url(route('dearsanta', ['santa' => '%s']), PHP_URL_PATH);
            $data = sscanf($path, $pathTheorical);
            $id = Hashids::decode($data[0]);
            $dearSanta = DearSanta::find($id[0]);

            $encrypter = new SymmetricalEncrypter(base64_decode($key));
            $this->assertEquals($santa['name'], $encrypter->decrypt($dearSanta->santa_name, false));
            $this->assertEquals($santa['email'], $encrypter->decrypt($dearSanta->santa_email, false));

            // Try to contact santa
            $response = $this->ajaxPost($path, [
                'g-recaptcha-response' => 'mocked',
                'key'                  => $key,
                'content'              => 'test dearsanta mail content',
            ]);

            $response
                ->assertStatus(200)
                ->assertJson([
                    'message' => 'Envoyé avec succès !'
                ]);

            Mail::assertSent(\App\Mail\DearSanta::class, function ($mail) use ($santa) {
                return $mail->hasTo($santa['email'], $santa['name']);
            });
        }
    }
}
