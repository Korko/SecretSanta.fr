<?php

namespace Tests\Feature;

use Mail;
use Crypt;
use Hashids;
use App\DearSanta;

class RequestDearSantaTest extends RequestCase
{
    public function testDearsanta(): void
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

        // For security issues, the key is only sent by mail and never stored
        // So fetch it from the mail
        $links = [];
        Mail::assertQueued(\App\Mail\TargetDrawn::class, function ($mail) use (&$links) {
            $links[] = $mail->dearSantaLink;

            return true;
        });
        $this->assertEquals(count($participants), count($links));

        foreach ($links as $id => $link) {
            $path = parse_url($link, PHP_URL_PATH);

            $key = base64_decode(parse_url($link, PHP_URL_FRAGMENT));
            Crypt::setKey($key);

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

            $this->assertEquals($santa['name'], $dearSanta->santa_name);
            $this->assertEquals($santa['email'], $dearSanta->santa_email);

            // Try to contact santa
            $response = $this->ajaxPost($path, [
                'g-recaptcha-response' => 'mocked',
                'key'                  => base64_encode($key),
                'content'              => 'test dearsanta mail content',
            ]);

            $response
                ->assertStatus(200)
                ->assertJson([
                    'message' => 'Envoyé avec succès !',
                ]);

            Mail::assertQueued(\App\Mail\DearSanta::class, function ($mail) use ($santa) {
                return $mail->hasTo($santa['email'], $santa['name']);
            });
        }
    }
}
