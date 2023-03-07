<?php

namespace Database\Factories;

use App\Models\Draw;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Draw>
 */
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
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'mail_title' => $this->faker->sentence(),
            'mail_body' => $this->faker->text(),
            'organizer_name' => $this->faker->name(),
            'organizer_email' => $this->faker->email(),
        ];
    }

    /**
     * Indicate that the draw has just ended.
     *
     * @return static
     */
    public function isFinished()
    {
        return $this->state(function () {
            return [
                'finished_at' => Carbon::now(),
            ];
        });
    }

    /**
     * Indicate that the draw is ready for pruning.
     *
     * @return static
     */
    public function isExpired()
    {
        return $this->state(function () {
            return [
                'finished_at' => Carbon::now()->subDays(Draw::DAYS_BEFORE_DELETION),
            ];
        });
    }
}
