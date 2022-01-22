<?php

namespace Database\Factories;

use App\Models\Exclusion;
use App\Models\Participant;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExclusionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Exclusion::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'participant_id' => Participant::factory(),
            'exclusion_id'   => Participant::factory(),
        ];
    }
}