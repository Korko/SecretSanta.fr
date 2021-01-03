<?php

namespace Database\Factories;

use App\Models\Draw;
use Illuminate\Database\Eloquent\Factories\Factory;

class DrawFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Draw::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
        'mail_title' => $this->faker->sentence,
        'mail_body'  => $this->faker->text,
        'expires_at' => $this->faker->dateTimeBetween('+1 day', '+1 month'),
    ];
    }

    public function expired()
    {
        return $this->state(function () {
            return [
        'expires_at' => $this->faker->dateTime('-1 hour'),
    ];
        });
    }
}
