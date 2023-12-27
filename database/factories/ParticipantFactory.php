<?php

namespace Database\Factories;

use App\Models\Draw;
use App\Models\Participant;
use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Participant>
 */
class ParticipantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ulid' => Str::ulid(),
            'draw_id' => Draw::factory(),
            'name' => $this->faker->name(),
            'email' => $this->faker->email(),
            'email_verified_at' => null,
            'target_id' => null,
        ];
    }

    /**
     * Indicate that the participant exclusions will
     */
    // TODO: check
    public function bijective(): static
    {
        return $this->afterCreating(function (Participant $participant, Draw $draw) {
            if ($draw->santas->count() % 2 !== 0) {
                throw new Exception('Cannot make bijective participants with odd number of them');
            }

            $participant->exclusions()->attach(
                $draw->santas
                    ->pluck('id')
                    ->filter(function (int $id) use ($participant) {
                        return floor(($id - 1) / 2) !== floor(($participant->id - 1) / 2);
                    })
            );
        });
    }
}
