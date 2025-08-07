<?php

namespace Database\Factories\Draw;

use App\Enums\DrawStatus;
use App\Models\Draw\Draw;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Draw\Draw>
 */
class DrawFactory extends Factory
{
    protected $model = Draw::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'uuid' => (string) Str::uuid(),
            'organizer_key_hash' => hash('sha256', $this->faker->text(32)),
            'master_key_encrypted' => base64_encode($this->faker->text(64)),
            'title_encrypted' => base64_encode($this->faker->sentence()),
            'description_encrypted' => base64_encode($this->faker->paragraph()),
            'organizer_name_encrypted' => base64_encode($this->faker->name()),
            'organizer_email_encrypted' => base64_encode($this->faker->email()),
            'status' => DrawStatus::DRAFT,
            'registration_deadline' => $this->faker->dateTimeBetween('now', '+30 days'),
            'auto_accept_participants' => $this->faker->boolean(),
            'allow_target_messages' => $this->faker->boolean(),
        ];
    }

    /**
     * Create a draft draw
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => DrawStatus::DRAFT,
            'drawn_at' => null,
            'revealed_at' => null,
        ]);
    }

    /**
     * Create an open registration draw
     */
    public function openForRegistration(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => DrawStatus::OPEN_REGISTRATION,
            'registration_deadline' => $this->faker->dateTimeBetween('+1 day', '+30 days'),
        ]);
    }

    /**
     * Create a closed registration draw
     */
    public function closedRegistration(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => DrawStatus::CLOSED_REGISTRATION,
            'registration_deadline' => $this->faker->dateTimeBetween('-30 days', 'now'),
        ]);
    }

    /**
     * Create a drawn draw
     */
    public function drawn(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => DrawStatus::DRAWN,
            'drawn_at' => $this->faker->dateTimeBetween('-7 days', 'now'),
        ]);
    }

    /**
     * Create a revealed draw
     */
    public function revealed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => DrawStatus::REVEALED,
            'drawn_at' => $this->faker->dateTimeBetween('-7 days', '-1 day'),
            'revealed_at' => $this->faker->dateTimeBetween('-1 day', 'now'),
        ]);
    }
}