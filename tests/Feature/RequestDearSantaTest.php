<?php

namespace Tests\Feature;

use App\Mail\DearSanta;
use App\Mail\TargetDrawn;
use App\Models\Participant;
use Crypt;
use Hashids;
use Illuminate\Support\Facades\URL;
use Mail;
use URLParser;

test('a participant can write to their santa', function () {
    Mail::fake();

    $participants = createAjaxDraw(3);

    // For security issues, the key is only sent by mail and never stored
    // So fetch it from the mail
    $links = [];
    Mail::assertSent(function (TargetDrawn $mail) use (&$links) {
        return $links[] = $mail->dearSantaLink;
    });
    assertEquals(count($participants), count($links));

    foreach ($links as $id => $link) {
        // Get the form page (just to check http code)
        $response = $this->get($link);
        assertEquals(200, $response->status(), $response->__toString());

        $santaId = array_search($id, array_column($participants, 'target'));
        $santa = $participants[$santaId];

        // Check data stored are decryptable
        $path = parse_url($link, PHP_URL_PATH);
        $santaTheorical = URLParser::parseByName('dearsanta', $path)->participant;

        assertEquals($santa['name'], $santaTheorical->santa->name);
        assertEquals($santa['email'], $santaTheorical->santa->email);

        // Try to contact santa
        ajaxPost(URL::signedRoute('dearsanta.contact', ['participant' => $santaTheorical->hash]), [
                'content' => 'test dearsanta mail content',
            ])
            ->assertJsonStructure(['message'])
            ->assertStatus(200);

        assertHasMailPushed(DearSanta::class, $santaTheorical->santa->email);
    }
});