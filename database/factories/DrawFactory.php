<?php

namespace Database\Factories;

use App\Models\Draw;
use Carbon\Carbon;
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
            'mail_title'      => $this->faker->sentence,
            'mail_body'       => $this->faker->text,
            'organizer_name'  => $this->faker->name,
            'organizer_email' => $this->faker->email,
        ];
    }

    public function finished()
    {
        return $this->state(function () {
            return [
                'finished_at' => Carbon::now()
            ];
        });
    }

    public function expired()
    {
        return $this->state(function () {
            return [
                'finished_at' => Carbon::now()->subDays(Draw::DAYS_BEFORE_DELETION)
            ];
        });
    }
}
