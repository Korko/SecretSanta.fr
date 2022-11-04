<?php

namespace Database\Factories;

use App\Models\Draw;
use App\Models\Participant;
use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Participant>
 */
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
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'draw_id' => Draw::factory(),
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'target_id' => null,
        ];
    }

    /**
     * Indicate that the participant exclusions will
     *
     * @return static
     */
    // TODO: check
    public function bijective()
    {
        return $this->afterCreating(function (Participant $participant, Draw $draw) {
            if ($draw->participants->count() % 2 !== 0) {
                throw new Exception('Cannot make bijective participants with odd number of them');
            }

            $participant->exclusions()->attach(
                $draw->participants
                    ->pluck('id')
                    ->filter(function ($id) use ($participant) {
                        return floor(($id - 1) / 2) !== floor(($participant->id - 1) / 2);
                    })
            );
        });
    }
}
