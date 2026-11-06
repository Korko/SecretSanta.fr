<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Draw;
use Illuminate\Database\Eloquent\Factories\Factory;

final class ParticipantFactory extends Factory
{
    /**
     * Define the model's default state.
     */
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
