<?php

namespace Database\Factories;

use App\Models\Draw;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Draw>
 */
class DrawFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'mail_title' => $this->faker->sentence(),
            'mail_body' => $this->faker->text(),
            'organizer_name' => $this->faker->name(),
            'organizer_email' => $this->faker->email(),
        ];
    }

    /**
     * Indicate that the organizer is also a participant
     */
    public function withParticipantOrganizer(): static
    {
        return $this->state(function () {
            return [
                'organizer_id' => Participant::factory(),
            ];
        });
    }

    /**
     * Indicate that the draw has just ended.
     */
    public function isFinished(): static
    {
        return $this->state(function () {
            return [
                'finished_at' => Carbon::now(),
            ];
        });
    }

    /**
     * Indicate that the draw is ready for pruning.
     */
    public function isExpired(): static
    {
        return $this->state(function () {
            return [
                'finished_at' => Carbon::now()->subDays(Draw::DAYS_BEFORE_DELETION),
            ];
        });
    }
}
