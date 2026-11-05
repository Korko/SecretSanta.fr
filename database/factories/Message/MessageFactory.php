<?php

namespace Database\Factories\Message;

use App\Models\Draw\Draw;
use App\Models\Draw\Participant;
use App\Models\Message\Message;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Message\Message>
 */
class MessageFactory extends Factory
{
    protected $model = Message::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'draw_id' => Draw::factory(),
            'from_participant_id' => Participant::factory(),
            'to_participant_id' => Participant::factory(),
            'encryption_version' => 2,
            'content_encrypted' => base64_encode($this->faker->paragraph()),
            'content_hash' => hash('sha256', $this->faker->paragraph()),
            'type' => $this->faker->randomElement(['to_secret_santa', 'to_target', 'system']),
            'status' => 'sent',
            'moderation_status' => 'ok',
            'moderation_data' => null,
        ];
    }

    /**
     * Create a reported message
     */
    public function reported(): static
    {
        return $this->state(fn (array $attributes) => [
            'moderation_status' => 'reported',
        ]);
    }

    /**
     * Create a reviewed message
     */
    public function reviewed(): static
    {
        return $this->state(fn (array $attributes) => [
            'moderation_status' => 'reviewing',
            'moderation_data' => ['reviewer_notes' => $this->faker->sentence()],
        ]);
    }

    /**
     * Create a message to secret santa
     */
    public function toSecretSanta(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'to_secret_santa',
        ]);
    }

    /**
     * Create a message to target
     */
    public function toTarget(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'to_target',
        ]);
    }

    /**
     * Create a system message
     */
    public function system(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'system',
        ]);
    }

    /**
     * Create message between specific participants
     */
    public function between(Participant $from, Participant $to): static
    {
        return $this->state(fn (array $attributes) => [
            'draw_id' => $from->draw_id,
            'from_participant_id' => $from->id,
            'to_participant_id' => $to->id,
        ]);
    }
}