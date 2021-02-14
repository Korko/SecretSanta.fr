<?php

namespace Database\Factories;

use App\Models\Draw;
use App\Models\Mail as MailModel;
use Illuminate\Database\Eloquent\Factories\Factory;

class MailFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = MailModel::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'delivery_status' => MailModel::CREATED,
            'draw_id' => Draw::factory(),
            'version' => $this->faker->numberBetween(0, 255),
        ];
    }
}
