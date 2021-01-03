<?php

namespace Tests\Feature;

use App\Mail\TargetDrawn;
use App\Models\Draw;
use App\Models\Participant;
use Crypt;
use Illuminate\Support\Facades\URL;
use Mail;

class RequestOrganizerTest extends RequestCase
{
    public function createDrawWithParticipants(int $participants): Draw
    {
        $this->assertGreaterThan(1, $participants);

        $draw = Draw::factory()->create();
        $draw->participants()->createMany(
            Participant::factory()->count($participants)->make()->toArray()
        );

        foreach ($draw->participants as $idx => $participant) {
            $target = $draw->participants[$idx - 1 >= 0 ? $idx - 1 : $participants - 1];
            $participant->target()->save($target);
        }

        return $draw;
    }

    public function testSendAgain(): void
    {
        Mail::fake();
        Mail::assertNothingSent();

        $draw = $this->createDrawWithParticipants(3);
        $participant = $draw->participants->first();

        // Check data can be changed
        $path = URL::signedRoute('organizerPanel.changeEmail', [
            'draw' => $draw->hash,
            'participant' => $participant->id,
        ]);

        $this->ajaxPost($path, [
                'email' => $participant->email,
            ])
            ->assertJson([
                'message' => 'Envoyé avec succès !',
            ])
            ->assertStatus(200);

        $this->assertHasMailPushed(TargetDrawn::class, $participant->email);
    }

    public function testChangeEmail(): void
    {
        Mail::fake();
        Mail::assertNothingSent();

        $draw = $this->createDrawWithParticipants(3);
        $participant = $draw->participants->first();

        // Check data can be changed
        $path = URL::signedRoute('organizerPanel.changeEmail', [
            'draw' => $draw->hash,
            'participant' => $participant->id,
        ]);

        $this->ajaxPost($path, [
                'email' => 'test@test2.com',
            ])
            ->assertJson([
                'message' => 'Modifié avec succès !',
            ])
            ->assertStatus(200);

        $before = $participant->email;
        $participant = Participant::find($participant->id);
        $after = $participant->email;

        $this->assertNotEquals($before, $after);
        $this->assertEquals('test@test2.com', $after);

        $this->assertHasMailPushed(TargetDrawn::class, $participant->email);
    }
}
