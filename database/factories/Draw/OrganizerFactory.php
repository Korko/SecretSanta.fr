<?php

namespace Database\Factories\Draw;

use App\Models\Draw\Draw;
use App\Models\Draw\Organizer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Draw\Organizer>
 */
class OrganizerFactory extends Factory
{
    protected $model = Organizer::class;

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
            'individual_key_hash' => hash('sha256', $this->faker->lexify(str_repeat('?', 32))),
            'master_key_encrypted' => base64_encode($this->faker->lexify(str_repeat('?', 64))),
            'encryption_version' => 2,
            'name_encrypted' => base64_encode($this->faker->name()),
            'email_encrypted' => base64_encode($this->faker->safeEmail()),
            'email_sent_at' => null,
            'email_opened_at' => null,
            'last_access_at' => null,
        ];
    }

    /**
     * Create an organizer with email sent
     */
    public function emailSent(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_sent_at' => now()->subHours(rand(1, 24)),
        ]);
    }

    /**
     * Create an organizer with email opened
     */
    public function emailOpened(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_sent_at' => now()->subHours(rand(2, 48)),
            'email_opened_at' => now()->subHours(rand(1, 24)),
        ]);
    }

    /**
     * Create an organizer with recent access
     */
    public function recentAccess(): static
    {
        return $this->state(fn (array $attributes) => [
            'last_access_at' => now()->subMinutes(rand(5, 120)),
        ]);
    }

    /**
     * Create organizer for specific draw
     */
    public function forDraw(Draw $draw): static
    {
        return $this->state(fn (array $attributes) => [
            'draw_id' => $draw->id,
        ]);
    }
}