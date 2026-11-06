<?php

namespace Database\Factories;

use App\Models\Mail as MailModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Mail>
 */
final class MailFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = MailModel::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'notification' => fake()->uuid(),
            'delivery_status' => MailModel::CREATED,
        ];
    }

    /**
     * Indicate that the mail has failed to be sent.
     */
    public function failed(): Factory
    {
        return $this->state(fn (): array => [
            'delivery_status' => MailModel::ERROR,
        ]);
    }
}
