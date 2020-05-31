<?php

namespace Tests\Feature;

use App\Mail\DearSanta;
use App\Mail\TargetDrawn;
use App\Participant;
use Crypt;
use Hashids;
use Mail;

class RequestDearSantaTest extends RequestCase
{
    public function testDearSanta(): void
    {
        Mail::fake();
        Mail::assertNothingSent();

        $participants = $this->createNewDraw(3);

        // For security issues, the key is only sent by mail and never stored
        // So fetch it from the mail
        $links = [];
        Mail::assertSent(function (TargetDrawn $mail) use (&$links) {
            return $links[] = $mail->dearSantaLink;
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
            $pathTheorical = parse_url(route('dearsanta', ['participant' => '%s']), PHP_URL_PATH);
            [$hash] = sscanf($path, $pathTheorical);
            $santaTheorical = Participant::findByHashOrFail($hash);

            $this->assertEquals($santa['name'], $santaTheorical->santa->name);
            $this->assertEquals($santa['email'], $santaTheorical->santa->email);

            // Try to contact santa
            $response = $this->ajaxPost(route('dearsanta.contact', ['participant' => $hash]), [
                'key'     => base64_encode(Crypt::getKey()),
                'content' => 'test dearsanta mail content',
            ]);

            $response
                ->assertStatus(200)
                ->assertJson([
                    'message' => 'Envoyé avec succès !',
                ]);

            $this->assertHasMailPushed(DearSanta::class, $santaTheorical->santa->email);
        }
    }
}
