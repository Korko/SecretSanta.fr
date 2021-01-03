<?php

namespace Tests\Feature;

use App\Models\Draw;

class CleanupTest extends RequestCase
{
    use \Illuminate\Foundation\Testing\DatabaseMigrations;
    use \Illuminate\Foundation\Testing\DatabaseTransactions;

    public function testDrawNotExpired(): void
    {
        $this->assertEquals(0, Draw::count());
        $draw = Draw::factory()->create();
        $this->assertEquals(1, Draw::count());
        $this->assertDatabaseHas('draws', ['id' => $draw->id]);

        Draw::cleanup();

        $this->assertEquals(1, Draw::count());
        $this->assertDatabaseHas('draws', ['id' => $draw->id]);
    }

    public function testDrawExpired(): void
    {
        $this->assertEquals(0, Draw::count());
        $draw = Draw::factory()->expired()->create();
        $this->assertEquals(1, Draw::count());
        $this->assertDatabaseHas('draws', ['id' => $draw->id]);

        Draw::cleanup();

        $this->assertEquals(0, Draw::count());
    }
}
