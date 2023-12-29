<?php

namespace Database\Factories;

use App\Enums\DrawStatus;
use App\Models\Draw;
use App\Models\Participant;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Draw>
 */
class DrawFactory extends Factory
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
            'title' => $this->faker->sentence(),
            'description' => $this->faker->text(),
            'organizer_id' => null,// Don't call ParticipantFactory here or it will loop
            'participant_organizer' => true,
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Draw $draw) {
            if ($draw->participant_organizer && $draw->participants()->count() > 0) {
                $draw->update([
                    'organizer_id' => $draw->participants()->first()->id,
                ]);
            } else {
                $draw->update([
                    'organizer_id' => Participant::factory()->for($draw)->create()->id,
                ]);
            }
        });
    }

    /**
     * Indicate that the organizer is also a participant
     */
    public function withNonParticipantOrganizer(): static
    {
        return $this->state(function () {
            return [
                'participant_organizer' => false,
            ];
        });
    }

    public function withOrganizerEmailVerified(): static
    {
        return $this->afterCreating(function (Draw $draw) {
            $draw->organizer->update([
                'email_verified_at' => Carbon::now(),
            ]);
        });
    }

    public function withParticipants(string|array $participants): static
    {
        return $this->has(
            Participant::factory()
                ->forEachSequence(...array_map(function ($participant) {
                    return [
                        'name' => $participant['name'] ?? $participant,
                        'email' => $participant['email'] ?? null,
                        //'exceptions' => $participant['exclusions'] ?? [],
                    ];
                }, (array) $participants)),
            'participants'
        );
    }

    public function withVerifiedParticipants(array $participants): static
    {
        $names = array_keys($participants);

        return $this->has(
            Participant::factory()
            ->forEachSequence(...array_map(function ($name, $email) {
                return [
                    'name' => $name,
                    'email' => $email,
                    'email_verified_at' => Carbon::now(),
                ];
            }, $names, $participants)),
            'participants'
        );
    }

    /**
     * Indicate that the pending draw has been validated and is ready to be processed.
     */
    public function isReady(): static
    {
        return $this->state(function () {
            return [
                'status' => DrawStatus::READY,
                'ready_at' => Carbon::now(),
            ];
        });
    }

    /**
     * Indicate that the draw has just ended.
     */
    public function isFinished(): static
    {
        return $this->state(function () {
            return [
                'status' => DrawStatus::FINISHED,
                'finished_at' => Carbon::now(),
            ];
        });
    }

    /**
     * Indicate that the draw is ready for pruning.
     */
    public function isExpired(): static
    {
        return $this->state(function () {
            return [
                'status' => DrawStatus::FINISHED,
                'finished_at' => Carbon::now()->subDays(Draw::DAYS_BEFORE_DELETION_AFTER_FINISH),
            ];
        });
    }
}
