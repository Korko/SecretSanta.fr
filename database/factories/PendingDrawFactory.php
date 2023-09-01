<?php

namespace Database\Factories;

use App\Enums\EmailAddressStatus;
use App\Models\PendingDraw;
use App\Models\PendingParticipant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\Sequence;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PendingDraw>
 */
class PendingDrawFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
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
                'organizer_id' => PendingParticipant::factory(),
            ];
        });
    }

    public function withEmailConfirmed(): static
    {
        return $this->state(function () {
            return [
                'email_status' => EmailAddressStatus::CONFIRMED,
            ];
        });
    }

    public function withParticipants(...$names): static
    {
        return $this->has(
            PendingParticipant::factory()
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
                'status' => PendingDraw::STATE_READY,
            ];
        });
    }
}
