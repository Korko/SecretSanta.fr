<?php

namespace Tests\Feature;

use Mail;
use Crypt;
use Hashids;
use App\Participant;

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

            // Get the form page (just to check http code)
            $response = $this->get($path);
            $this->assertEquals(200, $response->status(), $response->__toString());

            $santaId = array_search($id, array_column($participants, 'target'));
            $santa = $participants[$santaId];

            // Check data stored are decryptable
            $pathTheorical = parse_url(route('dearsanta', ['santa' => '%s']), PHP_URL_PATH);
            $data = sscanf($path, $pathTheorical);
            $id = Hashids::decode($data[0]);
            $santaTheorical = Participant::find($id[0]);

            $this->assertEquals($santa['name'], $santaTheorical->santa->name);
            $this->assertEquals($santa['email'], $santaTheorical->santa->email_address);

            // Try to contact santa
            $response = $this->ajaxPost($path, [
                'key'                  => base64_encode(Crypt::getKey()),
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
