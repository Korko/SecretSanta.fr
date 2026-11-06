<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Draw;
use App\Services\DrawHandler;
use Illuminate\Database\Eloquent\Factories\Factory;

final class DrawFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'mail_title' => fake()->sentence(),
            'mail_body' => fake()->text(),
            'expires_at' => fake()->dateTimeBetween('+1 day', '+1 month'),
            'organizer_name' => fake()->name(),
            'organizer_email' => fake()->email(),
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Draw $draw): void {
            if ($draw->participants->isNotEmpty()) {
                DrawHandler::solve($draw, $draw->participants);
            }
        });
    }

    /**
     * Indicate that the draw is expired.
     */
    public function expired(): Factory
    {
        return $this->state(fn (): array => [
            'expires_at' => fake()->dateTime('-1 hour'),
        ]);
    }
}
