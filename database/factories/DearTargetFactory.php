<?php

namespace Database\Factories;

use App\Enums\QuestionToSanta;
use App\Models\DearTarget;
use App\Models\Draw;
use App\Models\Participant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DearTarget>
 */
class DearTargetFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DearTarget::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'draw_id' => Draw::factory(),
            'sender_id' => Participant::factory(),
            'mail_type' => $this->faker->randomElement(QuestionToSanta::cases())->value,
        ];
    }

     /**
      * Indicate that the dearTarget is resendable.
      *
      * @return static
      */
     public function resendable()
     {
         return $this->afterCreating(function (DearTarget $dearTarget) {
             $dearTarget->mail->updated_at = $dearTarget->mail->updated_at->subSeconds(config('mail.resend_delay'));
             $dearTarget->mail->save();
         });
     }
}