<?php

namespace Database\Factories;

use App\Models\PendingDraw;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class PendingDrawFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PendingDraw::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'organizer_name' => $this->faker->name,
            'organizer_email' => $this->faker->email,
            'data' => [
                'participant-organizer' => '1',
                'participants'          => [],
                'title'                 => $this->faker->sentence,
                'content'               => $this->faker->text
            ]
        ];
    }

    public function validated()
    {
        return $this->state(function () {
            return [
                'status' => PendingDraw::STATE_VALIDATED
            ];
        });
    }
}
