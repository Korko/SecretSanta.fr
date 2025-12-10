<?php

namespace Database\Factories;

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
    public function definition(): array
    {
        return [
            'notification' => $this->faker->uuid(),
            'delivery_status' => MailModel::CREATED,
        ];
    }

    /**
     * Indicate that the mail has failed to be sent.
     *
     * @return Factory
     */
    public function failed(): Factory
    {
        return $this->state(function () {
            return [
                'delivery_status' => MailModel::ERROR,
            ];
        });
    }
}
