<?php

namespace Database\Factories;

use App\Enums\DrawStatus;
use App\Models\Draw;
use App\Models\Participant;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
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
            if(is_null($draw->organizer)) {
                $draw
                    ->organizer()
                    ->associate(
                        Participant::factory()
                            ->for($draw)
                            ->create()
                    )
                    ->save();
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

    public function withOrganizer(string|array $name, ?string $email = null, ?array $exclusions = []): static
    {
        return $this->has(
            Participant::factory([
                'name' => $name['name'] ?? $name,
                'email' => $name['email'] ?? $email,
                //'exceptions' => $name['exclusions'] ?? $exclusions,
            ]),
            'organizer'
        );
    }

    public function withParticipants(string|array $participantsData): static
    {
        $participantsData = array_map(
            fn($participantData) => !empty($participantData['name']) ? $participantData : ['name' => $participantData],
            (array) $participantsData
        );

        return $this->has(
            Participant::factory()
                ->forEachSequence(...array_map(function ($participant) {
                    return [
                        'name' => $participant['name'],
                        'email' => $participant['email'] ?? null
                    ];
                }, $participantsData)),
            'participants'
        )
        ->afterCreating(function (Draw $draw) use ($participantsData) {
            $draw->santas->each(function (Participant $participant) use ($draw, $participantsData) {
                $participantData = collect($participantsData)->first(function ($participantData) use ($participant) {
                    return $participant->name === $participantData['name'];
                });

                if(!empty($participantData['exclusions'])) {
                    foreach($participantData['exclusions'] as $exclusion) {
                        $participant->exclusions()->attach($draw->santas->first( function ($participant) use ($participantsData, $exclusion) {
                            return $participantsData[$exclusion]['name'] === $participant->name;
                        }), [
                            'draw_id' => $draw->id,
                        ]);
                    }
                }
            });
        });
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
