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
            'content_encrypted' => base64_encode($this->faker->paragraph()),
            'type' => $this->faker->randomElement(['text', 'image', 'suggestion']),
            'is_reported' => false,
            'is_reviewed' => false,
            'reviewer_notes' => null,
        ];
    }

    /**
     * Create a reported message
     */
    public function reported(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_reported' => true,
            'is_reviewed' => false,
            'reviewer_notes' => null,
        ]);
    }

    /**
     * Create a reviewed message
     */
    public function reviewed(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_reported' => true,
            'is_reviewed' => true,
            'reviewer_notes' => $this->faker->sentence(),
        ]);
    }

    /**
     * Create a text message
     */
    public function text(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'text',
        ]);
    }

    /**
     * Create an image message
     */
    public function image(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'image',
        ]);
    }

    /**
     * Create a suggestion message
     */
    public function suggestion(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'suggestion',
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