<?php

namespace Tests\Unit;

use App\Draw;
use Tests\TestCase;
use App\Participant;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EncryptsAttributesTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    public function testEncrypts()
    {
        // Define a random title and create a draw with it
        $originalTitle = $this->faker()->sentence;
        $draw = factory(Draw::class)->create([
            'email_title' => $originalTitle,
        ]);
        $encryptionKey = $draw->getEncryptionKey();

        // When fetching a draw, we can get the title still encrypted
        $draw = Draw::find($draw->id);
        $this->assertNotEquals($originalTitle, $draw->email_title);

        // Just by setting the key, we can get the decrypted value
        $draw->setEncryptionKey($encryptionKey);
        $this->assertEquals($originalTitle, $draw->email_title);
    }

    public function testArray()
    {
        $originalTarget = (object) ['name' => $this->faker()->name];
        $participant = factory(Participant::class)->create([
            'target' => $originalTarget,
        ]);
        $encryptionKey = $participant->getEncryptionKey();

        // When fetching a participant, we can get the target still encrypted
        $participant = Participant::find($participant->id);
        $this->assertNotEquals($originalTarget, $participant->target);

        // Just by setting the key, we can get the decrypted value
        $participant->setEncryptionKey($encryptionKey);
        $this->assertEquals($originalTarget, $participant->target);
    }

    public function testArray2()
    {
        $originalTarget = (object) ['name' => $this->faker()->name];

        $participant = factory(Participant::class)->make();
        $participant->target = $originalTarget;
        $participant->save();

        $encryptionKey = $participant->getEncryptionKey();

        // When fetching a participant, we can get the target still encrypted
        $participant = Participant::find($participant->id);
        $this->assertNotEquals($originalTarget, $participant->target);

        // Just by setting the key, we can get the decrypted value
        $participant->setEncryptionKey($encryptionKey);
        $this->assertEquals($originalTarget, $participant->target);
    }
}
