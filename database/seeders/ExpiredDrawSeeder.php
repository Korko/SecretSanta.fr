<?php

namespace Database\Seeders;

use App\Models\DearSanta;
use App\Models\DearTarget;
use App\Models\Draw;
use App\Models\Participant;
use App\Services\DrawHandler;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Notification;

class ExpiredDrawSeeder extends Seeder
{
    public function run(): void
    {
        Notification::fake();

        // Generate Draw
        $draw = Draw::factory()
            ->isExpired()
            ->hasParticipants(10)
            ->create();

        // Attach a random exclusion for each participant (just to spice things up)
        $draw->participants->each(function (Participant $participant) use ($draw) {
            $participant
                ->exclusions()
                ->attach($draw->participants->except($participant->id)->random(), [
                    'draw_id' => $draw->id,
                ]);
        });

        DrawHandler::solve($draw, $draw->participants);

        // Fake some communications
        for ($i = 0; $i < 10; $i++) {
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

        $draw->save();
    }
}
