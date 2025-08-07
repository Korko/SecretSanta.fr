<?php

namespace Database\Factories\Draw;

use App\Enums\ParticipantStatus;
use App\Models\Draw\Draw;
use App\Models\Draw\Participant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Draw\Participant>
 */
class ParticipantFactory extends Factory
{
    protected $model = Participant::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'draw_id' => Draw::factory(),
            'uuid' => (string) Str::uuid(),
            'individual_key_hash' => hash('sha256', $this->faker->text(32)),
            'master_key_encrypted' => base64_encode($this->faker->text(64)),
            'name_encrypted' => base64_encode($this->faker->name()),
            'email_encrypted' => base64_encode($this->faker->unique()->email()),
            'status' => ParticipantStatus::PENDING,
            'is_organizer' => false,
            'assigned_to_participant_id' => null,
            'accepted_at' => null,
        ];
    }

    /**
     * Create a pending participant
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ParticipantStatus::PENDING,
            'accepted_at' => null,
        ]);
    }

    /**
     * Create an accepted participant
     */
    public function accepted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ParticipantStatus::ACCEPTED,
            'accepted_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
        ]);
    }

    /**
     * Create a declined participant
     */
    public function declined(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ParticipantStatus::DECLINED,
            'accepted_at' => null,
        ]);
    }

    /**
     * Create an organizer participant
     */
    public function organizer(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ParticipantStatus::ACCEPTED,
            'is_organizer' => true,
            'accepted_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
        ]);
    }

    /**
     * Create a participant assigned to another
     */
    public function assignedTo(Participant $participant): static
    {
        return $this->state(fn (array $attributes) => [
            'assigned_to_participant_id' => $participant->id,
        ]);
    }
}