<?php

namespace Database\Factories;

use App\Models\DearSanta;
use App\Models\Mail;
use App\Models\Participant;
use Illuminate\Database\Eloquent\Factories\Factory;

class DearSantaFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DearSanta::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'sender_id'   => Participant::factory(),
            'mail_body'   => $this->faker->text,
        ];
    }

    /**
     * Indicate that the dearSanta is resendable.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function resendable()
    {
        return $this->afterCreating(function (DearSanta $dearSanta) {
            $dearSanta->mail->updated_at = $dearSanta->mail->updated_at->subSeconds(config('mail.resend_delay'));
            $dearSanta->mail->save();
        });
    }
}
