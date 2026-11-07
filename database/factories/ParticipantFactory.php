<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Draw;
use App\Models\Participant;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Participant> */
final class ParticipantFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'draw_id' => Draw::factory(),
            'name' => fake()->name(),
            'email' => fake()->email(),
            'target_id' => null,
        ];
    }
}
