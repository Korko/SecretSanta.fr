<?php

namespace Database\Factories;

use App\Enums\EmailStatus;
use App\Models\Draw\Draw;
use App\Models\Draw\Organizer;
use App\Models\Draw\Participant;
use App\Models\Email\EmailDelivery;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Email\EmailDelivery>
 */
class EmailDeliveryFactory extends Factory
{
    protected $model = EmailDelivery::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $recipientType = $this->faker->randomElement([Participant::class, Organizer::class]);
        
        return [
            'draw_id' => Draw::factory(),
            'recipient_type' => $recipientType,
            'recipient_id' => $recipientType === Participant::class ? 
                Participant::factory() : 
                Organizer::factory(),
            'email_type' => $this->faker->randomElement([
                'invitation', 
                'reminder', 
                'results', 
                'participant_invitation', 
                'participant_reminder',
                'organizer_invitation'
            ]),
            'email_address' => $this->faker->safeEmail(),
            'subject' => $this->faker->sentence(),
            'message_id' => (string) Str::uuid(),
            'status' => $this->faker->randomElement(EmailStatus::cases()),
            'attempts_count' => $this->faker->numberBetween(0, 3),
            'metadata' => $this->faker->optional()->passthrough([
                'test_data' => true,
                'created_by' => 'factory',
                'notes' => $this->faker->sentence(),
            ]),
        ];
    }

    /**
     * Configure the model factory for pending status.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => EmailStatus::PENDING,
            'sent_at' => null,
            'delivered_at' => null,
            'bounced_at' => null,
            'bounce_reason' => null,
        ]);
    }

    /**
     * Configure the model factory for sent status.
     */
    public function sent(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => EmailStatus::SENT,
            'sent_at' => $this->faker->dateTimeBetween('-1 hour', 'now'),
            'delivered_at' => null,
            'bounced_at' => null,
            'bounce_reason' => null,
        ]);
    }

    /**
     * Configure the model factory for delivered status.
     */
    public function delivered(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => EmailStatus::DELIVERED,
            'sent_at' => $this->faker->dateTimeBetween('-2 hours', '-1 hour'),
            'delivered_at' => $this->faker->dateTimeBetween('-1 hour', 'now'),
            'bounced_at' => null,
            'bounce_reason' => null,
        ]);
    }

    /**
     * Configure the model factory for bounced status.
     */
    public function bounced(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => EmailStatus::BOUNCED,
            'sent_at' => $this->faker->dateTimeBetween('-2 hours', '-1 hour'),
            'delivered_at' => null,
            'bounced_at' => $this->faker->dateTimeBetween('-1 hour', 'now'),
            'bounce_reason' => $this->faker->randomElement([
                'Utilisateur inconnu',
                'Boîte mail inexistante', 
                'Boîte mail pleine',
                'Message trop volumineux',
                'Détecté comme spam',
                'Timeout de connexion'
            ]),
        ]);
    }

    /**
     * Configure the model factory for participant recipients.
     */
    public function forParticipant(Participant $participant = null): static
    {
        return $this->state(fn (array $attributes) => [
            'recipient_type' => Participant::class,
            'recipient_id' => $participant?->id ?? Participant::factory(),
            'draw_id' => $participant?->draw_id ?? Draw::factory(),
            'email_type' => $this->faker->randomElement([
                'participant_invitation',
                'participant_reminder',
                'results'
            ]),
        ]);
    }

    /**
     * Configure the model factory for organizer recipients.
     */
    public function forOrganizer(Organizer $organizer = null): static
    {
        return $this->state(fn (array $attributes) => [
            'recipient_type' => Organizer::class,
            'recipient_id' => $organizer?->id ?? Organizer::factory(),
            'draw_id' => $organizer?->draw_id ?? Draw::factory(),
            'email_type' => $this->faker->randomElement([
                'organizer_invitation',
                'reminder',
                'results'
            ]),
        ]);
    }

    /**
     * Configure the model factory with multiple attempts.
     */
    public function withRetries(int $attempts = 2): static
    {
        return $this->state(fn (array $attributes) => [
            'attempts_count' => $attempts,
            'last_attempt_at' => $this->faker->dateTimeBetween('-1 hour', 'now'),
            'metadata' => [
                'retry_attempts' => $attempts,
                'last_error' => $attempts > 1 ? 'Temporary failure' : null,
            ],
        ]);
    }

    /**
     * Configure the model factory as a resend email.
     */
    public function resend(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_type' => $attributes['email_type'] . '_resend',
            'attempts_count' => $this->faker->numberBetween(1, 3),
            'metadata' => [
                'is_resend' => true,
                'resend_reason' => 'Requested by organizer',
                'original_send_at' => $this->faker->dateTimeBetween('-1 week', '-1 day'),
            ],
        ]);
    }
}