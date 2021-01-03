<?php
namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;

class MailFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \App\Models\Mail::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
        'delivery_status' => App\Models\Mail::CREATED,
        'draw_id' => \App\Models\Draw::factory(),
        'version' => $this->faker->numberBetween(0, 255),
    ];
    }
}
