<?php

namespace Tests\Feature;

use App\Draw;
use App\Mail\TargetDrawn;
use App\Participant;
use Crypt;
use Queue;

class RequestOrganizerTest extends RequestCase
{
    public function createDrawWithParticipants(int $participants): Draw
    {
        $this->assertGreaterThan(1, $participants);

        $draw = factory(Draw::class)->create();
        $draw->participants()->createMany(
            factory(Participant::class, $participants)->make()->toArray()
        );

        foreach ($draw->participants as $idx => $participant) {
            $target = $draw->participants[$idx - 1 >= 0 ? $idx - 1 : $participants - 1];
            $participant->target()->save($target);
        }

        return $draw;
    }

    public function test_send_again(): void
    {
        Queue::fake();

        $draw = $this->createDrawWithParticipants(3);
        $participant = $draw->participants->first();

        // Check data can be changed
        $path = route('organizerPanel.resendEmail', [
            'draw' => $draw->id,
            'participant' => $participant,
        ]);
        $response = $this->ajaxPost($path, [
            'key' => base64_encode(Crypt::getKey()),
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Envoyé avec succès !',
            ]);

        $this->assertQueueHasMailPushed(TargetDrawn::class, $participant->email);
    }

    public function testChangeEmail(): void
    {
        Queue::fake();

        $draw = $this->createDrawWithParticipants(3);
        $participant = $draw->participants->first();

        // Check data can be changed
        $path = route('organizerPanel.changeEmail', [
            'draw' => $draw->id,
            'participant' => $participant,
        ]);
        $response = $this->ajaxPost($path, [
            'key' => base64_encode(Crypt::getKey()),
            'email' => 'test@test2.com',
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Modifié avec succès !',
            ]);

        $before = $participant->email;
        $participant = Participant::find($participant->id);
        $after = $participant->email;

        $this->assertNotEquals($before, $after);
        $this->assertEquals('test@test2.com', $after);

        $this->assertQueueHasMailPushed(TargetDrawn::class, $participant->email);
    }
}
