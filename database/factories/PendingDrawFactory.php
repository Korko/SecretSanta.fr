<?php

namespace Database\Factories;

use App\Models\PendingDraw;
use Illuminate\Database\Eloquent\Factories\Factory;

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
            'organizer_name' => $this->faker->name(),
            'organizer_email' => $this->faker->email(),
            'data' => [
                'participant-organizer' => '1',
                'participants' => [],
                'title' => $this->faker->sentence(),
                'content' => $this->faker->text(),
            ],
        ];
    }

    /**
     * Indicate that the pending draw has been validated and is ready to be processed.
     */
    public function ready(): static
    {
        return $this->state(function () {
            return [
                'status' => PendingDraw::STATE_READY,
            ];
        });
    }
}
