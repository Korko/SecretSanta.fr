<?php

namespace Database\Factories;

use App\Models\PendingDraw;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PendingParticipant>
 */
class PendingParticipantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'pending_draw_id' => PendingDraw::factory(),
            'name' => $this->faker->name(),
            'email' => $this->faker->email(),
            'exclusions' => []
        ];
    }
}
