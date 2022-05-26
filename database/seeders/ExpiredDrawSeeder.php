<?php

namespace Database\Seeders;

use App\Models\DearSanta;
use App\Models\DearTarget;
use App\Models\Draw;
use App\Models\Exclusion;
use App\Models\Participant;
use App\Services\DrawHandler;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Notification;

class ExpiredDrawSeeder extends Seeder
{
    public function run()
    {
        Notification::fake();

        // Generate Draw
        $draw = Draw::factory()
            ->has(
                Participant::factory()
                    ->count(10)
            )
            ->create();

        // Attach a random exclusion for each participant (just to spice things up)
        $draw->participants->each(function ($participant) use ($draw) {
            $participant->exclusions()->attach($draw->participants->except($participant->id)->random());
        });

        DrawHandler::solve($draw, $draw->participants);

        // Fake some communications
        for($i = 0; $i < 10; $i++) {
            $target = $draw->participants->random();
            DearSanta::factory()
                ->for($draw, 'draw')
                ->for($target, 'sender')
                ->create();

            $santa = $draw->participants->random();
            DearTarget::factory()
                ->for($draw, 'draw')
                ->for($santa, 'sender')
                ->create();
        }

        // Mark the draw as expired
        $draw->finished_at = Carbon::now()->subDays(Draw::DAYS_BEFORE_DELETION);
        $draw->save();
    }
}
