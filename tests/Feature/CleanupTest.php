<?php

namespace Tests\Feature;

use App\DearSantaDraw;
use App\Draw;

class CleanupTest extends RequestCase
{
    use \Illuminate\Foundation\Testing\DatabaseMigrations;

    public function testDrawNotExpired()
    {
        $this->assertEquals(0, Draw::count());
        $draw = factory(Draw::class)->create();
        $this->assertEquals(1, Draw::count());
        $this->assertDatabaseHas('draws', ['id' => $draw->id]);

        Draw::cleanup();

        $this->assertEquals(1, Draw::count());
        $this->assertDatabaseHas('draws', ['id' => $draw->id]);
    }

    public function testDrawExpired()
    {
        $this->assertEquals(0, Draw::count());
        $draw = factory(Draw::class)->states('expired')->create();
        $this->assertEquals(1, Draw::count());
        $this->assertDatabaseHas('draws', ['id' => $draw->id]);

        Draw::cleanup();

        $this->assertEquals(0, Draw::count());
    }

    public function testDearSantaDrawNotExpired()
    {
        $this->assertEquals(0, DearSantaDraw::count());
        $draw = factory(DearSantaDraw::class)->create();
        $this->assertEquals(1, DearSantaDraw::count());
        $this->assertDatabaseHas('dear_santa_draws', ['id' => $draw->id]);

        DearSantaDraw::cleanup();

        $this->assertEquals(1, DearSantaDraw::count());
        $this->assertDatabaseHas('dear_santa_draws', ['id' => $draw->id]);
    }

    public function testDearSantaDrawExpired()
    {
        $this->assertEquals(0, DearSantaDraw::count());
        $draw = factory(DearSantaDraw::class)->states('expired')->create();
        $this->assertEquals(1, DearSantaDraw::count());
        $this->assertDatabaseHas('dear_santa_draws', ['id' => $draw->id]);

        DearSantaDraw::cleanup();

        $this->assertEquals(0, DearSantaDraw::count());
    }
}

