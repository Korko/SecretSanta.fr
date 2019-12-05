<?php

namespace Tests\Unit;

use DB;
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
        $originalTitle = $this->faker()->sentence;
        $draw = factory(Draw::class)->create([
            'email_title' => $originalTitle,
        ]);

        // Ensure decryption is done when using Eloquent
        $draw = Draw::find($draw->id);
        $this->assertEquals($originalTitle, $draw->email_title);

        // Ensure encryption was done in database
        $draw = DB::select('select email_title from draws where id = ?', [$draw->id])[0];
        $this->assertNotEquals($draw->email_title, $originalTitle);
    }

    public function testArray()
    {
        $originalTarget = (object) ['name' => $this->faker()->name];

        $participant = factory(Participant::class)->create([
            'target' => $originalTarget,
        ]);

        // Ensure decryption is done when using Eloquent
        $participant = Participant::find($participant->id);
        $this->assertEquals($originalTarget, $participant->target);

        // Ensure encryption was done in database
        $participant = DB::select('select target from participants where id = ?', [$participant->id])[0];
        $this->assertNotEquals($participant->target, $originalTarget);
    }

    public function testArray2()
    {
        $originalTarget = (object) ['name' => $this->faker()->name];

        $participant = factory(Participant::class)->make();
        $participant->target = $originalTarget;
        $participant->save();

        // Ensure decryption is done when using Eloquent
        $participant = Participant::find($participant->id);
        $this->assertEquals($originalTarget, $participant->target);

        // Ensure encryption was done in database
        $participant = DB::select('select target from participants where id = ?', [$participant->id])[0];
        $this->assertNotEquals($participant->target, $originalTarget);
    }
}
