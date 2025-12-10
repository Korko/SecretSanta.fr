<?php

namespace Database\Factories;

use App\Models\Draw;
use App\Services\DrawHandler;
use Illuminate\Database\Eloquent\Factories\Factory;

class DrawFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'mail_title' => $this->faker->sentence(),
            'mail_body' => $this->faker->text(),
            'expires_at' => $this->faker->dateTimeBetween('+1 day', '+1 month'),
            'organizer_name' => $this->faker->name(),
            'organizer_email' => $this->faker->email(),
        ];
    }

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterCreating(function (Draw $draw) {
            if ($draw->participants->count() > 0) {
                DrawHandler::solve($draw, $draw->participants);
            }
        });
    }

    /**
     * Indicate that the draw is expired.
     *
     * @return Factory
     */
    public function expired()
    {
        return $this->state(function () {
            return [
                'expires_at' => $this->faker->dateTime('-1 hour'),
            ];
        });
    }
}
