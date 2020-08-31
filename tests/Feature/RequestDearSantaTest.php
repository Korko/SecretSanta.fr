<?php

namespace Tests\Feature;

use App\Mail\DearSanta;
use App\Mail\TargetDrawn;
use App\Models\Participant;
use Crypt;
use Hashids;
use Illuminate\Support\Facades\URL;
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
            // Get the form page (just to check http code)
            $response = $this->get($link);
            $this->assertEquals(200, $response->status(), $response->__toString());

            $santaId = array_search($id, array_column($participants, 'target'));
            $santa = $participants[$santaId];

            // Check data stored are decryptable
            $path = parse_url($link, PHP_URL_PATH);
            $santaTheorical = Participant::findByDearSantaUrlOrFail($path);

            $this->assertEquals($santa['name'], $santaTheorical->santa->name);
            $this->assertEquals($santa['email'], $santaTheorical->santa->email);

            // Try to contact santa
            $response = $this->ajaxPost(URL::signedRoute('dearsanta.contact', ['participant' => $santaTheorical->hash]), [
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
