<?php

namespace Database\Factories;

use App\Models\DearSanta;
use App\Models\Draw;
use App\Models\Participant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DearSanta>
 */
class DearSantaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'draw_id' => Draw::factory(),
            'sender_id' => Participant::factory(),
            'mail_body' => $this->faker->text(),
        ];
    }

    /**
     * Indicate that the dearSanta is resendable.
     */
    public function resendable(): static
    {
        return $this->afterCreating(function (DearSanta $dearSanta) {
            $dearSanta->mail->updated_at = $dearSanta->mail->updated_at->subSeconds(config('mail.resend_delay'));
            $dearSanta->mail->save();
        });
    }
}
