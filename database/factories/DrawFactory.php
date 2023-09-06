<?php

namespace Database\Factories;

use App\Models\Draw;
use App\Models\Participant;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Support\Str;

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
            'ulid' => Str::ulid(),
            'title' => $this->faker->sentence(),
            'description' => $this->faker->text(),
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

    public function withOrganizerEmailVerified(): static
    {
        return $this->state(function () {
            return [
                'organizer_email_verified_at' => Carbon::now(),
            ];
        });
    }

    public function withParticipants(...$names): static
    {
        return $this->has(
            Participant::factory()
                ->count(count($names))
                ->sequence(fn (Sequence $sequence) => ['name' => $names[$sequence->index]]),
            'participants'
        );
    }

    /**
     * Indicate that the pending draw has been validated and is ready to be processed.
     */
    public function isReady(): static
    {
        return $this->state(function () {
            return [
                'ready_at' => Carbon::now(),
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
