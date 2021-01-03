<?php
namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;

class ParticipantFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \App\Models\Participant::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
        'draw_id'   => \App\Models\Draw::factory(),
        'name'      => $this->faker->name,
        'email'     => $this->faker->email,
        'target_id' => null,
        'mail_id'   => \App\Models\Mail::factory(),
    ];
    }
}
