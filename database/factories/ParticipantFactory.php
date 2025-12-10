<?php

namespace Database\Factories;

use App\Models\Draw;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

class ParticipantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'draw_id' => Draw::factory(),
            'name' => $this->faker->name(),
            'email' => $this->faker->email(),
            'target_id' => null,
        ];
    }
}
