<?php

namespace Tests\Feature;

use App\Models\Draw;
use App\Models\Participant;
use App\Models\Exclusion;
use App\Models\DearSanta;
use App\Models\Mail;
use Database\Seeders\ExpiredDrawSeeder;

it('cleans up expired draws', function ($drawNotExpired, $drawExpired) {
    artisan('model:prune', ['--model' => [Draw::class]])->assertSuccessful();

    assertModelExists($drawNotExpired);
    assertModelMissing($drawExpired);
})->with('basic draw', 'expired draw');

it('cleans up everything', function () {
    seed(ExpiredDrawSeeder::class);

    assertModelCountDiffer(Draw::class, 0);
    assertModelCountDiffer(Participant::class, 0);
    assertModelCountDiffer(Exclusion::class, 0);
    assertModelCountDiffer(DearSanta::class, 0);
    assertModelCountDiffer(Mail::class, 0);

    artisan('model:prune', ['--model' => [Draw::class]])->assertSuccessful();

    assertModelCount(Draw::class, 0);
    assertModelCount(Participant::class, 0);
    assertModelCount(Exclusion::class, 0);
    assertModelCount(DearSanta::class, 0);
    assertModelCount(Mail::class, 0);
});