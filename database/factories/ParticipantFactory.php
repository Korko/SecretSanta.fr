<?php

namespace Database\Factories;

use App\Models\Draw;
use App\Models\Mail as MailModel;
use App\Models\Participant;
use App\Services\DrawHandler;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

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

    /**
     * Call the "after creating" callbacks for the given model instances.
     *
     * @param  \Illuminate\Support\Collection  $participants
     * @param  \Illuminate\Database\Eloquent\Model|null  $draw
     * @return void
     */
    protected function callAfterCreating(Collection $participants, ?Model $draw = null)
    {
        DrawHandler::solve($draw, $participants);
    }
}
