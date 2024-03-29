<?php

namespace Database\Seeders;

use App\Models\Draw;
use App\Models\Participant;
use App\Services\DrawHandler;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\URL;
use Notification;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Notification::fake();

        // Generate Draw
        $draw = Draw::factory()
            ->hasParticipants(10)
            ->create();

        // Attach a random exclusion for each participant (just to spice things up)
        $draw->santas->each(function ($participant) use ($draw) {
            $participant->exclusions()->attach($draw->santas->except($participant->id)->random());
        });

        DrawHandler::solve($draw);

        $this->command->line("Organizer panel: ".URL::hashedRoute('participant.index', ['participant' => $draw->organizer]));
    }
}
