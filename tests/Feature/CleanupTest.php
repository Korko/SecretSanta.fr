<?php

namespace Tests\Feature;

use App\Models\Draw;

it('does not cleanup non expired draws', function () {
    $draw = Draw::factory()->create();

    Draw::cleanup();

    assertNotNull($draw->fresh());
});

it('cleans up expired draws', function () {
    $draw = Draw::factory()->expired()->create();

    Draw::cleanup();

    assertNull($draw->fresh());
});