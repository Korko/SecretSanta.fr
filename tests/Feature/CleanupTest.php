<?php

namespace Tests\Feature;

use App\Models\DearSanta;
use App\Models\DearTarget;
use App\Models\Draw;
use App\Models\Mail;
use App\Models\Participant;
use Database\Seeders\ExpiredDrawSeeder;

it('cleans up expired draws', function (Draw $drawNotExpired, Draw $drawExpired) {
    artisan('model:prune', ['--model' => [Draw::class]])->assertSuccessful();

    expect($drawNotExpired)->toExists();
    expect($drawExpired)->not->toExists();
})->with('basic draw', 'expired draw');

it('cleans up everything', function () {
    seed(ExpiredDrawSeeder::class);

    expect(Draw::class)->not->toHaveCount(0);
    expect(Participant::class)->not->toHaveCount(0);
    expect(DearSanta::class)->not->toHaveCount(0);
    expect(DearTarget::class)->not->toHaveCount(0);
    expect(Mail::class)->not->toHaveCount(0);

    artisan('model:prune', ['--model' => [Draw::class]])->assertSuccessful();

    expect(Draw::class)->toHaveCount(0);
    expect(Participant::class)->toHaveCount(0);
    expect(DearSanta::class)->toHaveCount(0);
    expect(DearTarget::class)->toHaveCount(0);
    expect(Mail::class)->toHaveCount(0);
});
