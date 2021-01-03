<?php

namespace Database\Factories;

use App\Models\Draw;
use App\Models\Mail as MailModel;
use App\Models\Participant;
use Illuminate\Database\Eloquent\Factories\Factory;

class ParticipantFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Participant::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
        'draw_id'   => Draw::factory(),
        'name'      => $this->faker->name,
        'email'     => $this->faker->email,
        'target_id' => null,
        'mail_id'   => MailModel::factory(),
    ];
    }
}
