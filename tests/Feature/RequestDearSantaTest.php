<?php

namespace Tests\Feature;

use App\Participant;
use Crypt;
use Hashids;
use Queue;

class RequestDearSantaTest extends RequestCase
{
    public function testDearsanta(): void
    {
        Queue::fake();

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
            'participants'    => $participants,
            'title'           => 'test mail title',
            'content-email'   => 'test mail {SANTA} => {TARGET}',
            'data-expiration' => date('Y-m-d', strtotime('+2 days')),
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Envoyé avec succès !',
            ]);

        // For security issues, the key is only sent by mail and never stored
        // So fetch it from the mail
        $links = [];
        Queue::assertPushed(\App\Jobs\SendMail::class, function ($job) use (&$links) {
            if ($job->getMailable() instanceof \App\Mail\TargetDrawn) {
                $links[] = $job->getMailable()->dearSantaLink;
            }

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
            $id = Hashids::decode($data[0])[0];
            $santaTheorical = Participant::find($id);

            $this->assertEquals($santa['name'], $santaTheorical->santa->name);
            $this->assertEquals($santa['email'], $santaTheorical->santa->address);

            // Try to contact santa
            $response = $this->ajaxPost(route('dearsanta.contact', ['santa' => $data[0]]), [
                'key'     => base64_encode(Crypt::getKey()),
                'content' => 'test dearsanta mail content',
            ]);

            $response
                ->assertStatus(200)
                ->assertJson([
                    'message' => 'Envoyé avec succès !',
                ]);

            Queue::assertPushed(\App\Jobs\SendMail::class, function ($job) {
                return $job->getMailable() instanceof \App\Mail\DearSanta;
            });
        }
    }
}
