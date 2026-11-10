<?php

declare(strict_types=1);

namespace App\Models;

use App\Casts\EncryptedString;
use App\Collections\ParticipantsCollection;
use App\Enums\DeliveryStatus;
use App\Services\DrawHandler;
use Carbon\CarbonImmutable;
use Database\Factories\DrawFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Date;
use STS\Metrics\Facades\Metrics;
use STS\Metrics\Metric;

/**
 * @property CarbonImmutable $expires_at
 * @property CarbonImmutable|null $created_at
 * @property string $mail_title
 * @property string $mail_body
 * @property string $organizer_name
 * @property string $organizer_email
 * @property bool $next_solvable
 * @property CarbonImmutable|null $targets_revealed_at
 * @property-read bool $canRedraw
 * @property-read CarbonImmutable $deleted_at
 * @property CarbonImmutable|null $organizer_received_at
 * @property int|null $organizer_participant_id
 * @property-read Participant|null $organizer
 * @property-read bool $expired
 * @property-read string $metricId
 * @property-read ParticipantsCollection $participants
 * @property list<string>|null $refused_emails
 */
#[Fillable(['mail_title', 'mail_body', 'expires_at', 'next_solvable', 'organizer_name', 'organizer_email', 'organizer_participant_id'])]
final class Draw extends Model
{
    /** @use HasFactory<DrawFactory> */
    use HasFactory;

    use HashId;
    use Notifiable;
    use Prunable;

    // Remove everything N weeks after the expiration_date
    public const int WEEKS_BEFORE_DELETION = 3;

    /** @param array{touch?: bool|null} $options */
    public function save(array $options = []): bool
    {
        $this->expires_at ??= Date::now()->startOfDay()->addDays(7);

        return parent::save($options);
    }

    /**
     * Get the prunable model query.
     */
    /** @return Builder<self> */
    public function prunable(): Builder
    {
        return self::query()->where('expires_at', '<=', Date::now()->subWeeks(self::WEEKS_BEFORE_DELETION));
    }

    /** @return HasMany<Participant, $this> */
    public function participants(): HasMany
    {
        return $this->hasMany(Participant::class);
    }

    public function createMetric(string $name): Metric
    {
        return Metrics::create($name)
            ->setTags([
                'draw' => $this->metricId,
            ]);
    }

    // Participants and exclusions, reusable to prefill the form next time (organizer panel + recap email)
    public function initialCsv(): string
    {
        return $this->participants->toCsv();
    }

    public function initialCsvName(): string
    {
        return 'secretsanta_'.$this->expires_at->isoFormat('YYYY-MM-DD').'_init.csv';
    }

    // Addresses that reported the target email ("not me" / "not taking part") can't be used again in this draw,
    // even after removing and re-adding the person: only an HMAC is kept, the address itself is purged with the participant
    // Like a participant opening their dearSanta page, opening the panel proves the organizer got their email
    public function organizerReceived(): bool
    {
        return $this->organizer_received_at !== null
            || $this->organizer?->mail->delivery_status === DeliveryStatus::Received;
    }

    public function refusesEmail(string $email): bool
    {
        return array_intersect($this->emailFingerprints($email), $this->refused_emails ?? []) !== [];
    }

    public function refuseEmail(string $email, bool $refused = true): void
    {
        $fingerprints = array_diff($this->refused_emails ?? [], $this->emailFingerprints($email));
        $this->refused_emails = array_values($refused ? [...$fingerprints, $this->emailFingerprints($email)[0]] : $fingerprints);
        $this->save();
    }

    protected static function booted(): void
    {
        self::deleting(function (self $draw): void {
            $draw->participants()->lazy()->each(fn (Participant $participant): ?bool => $participant->delete());
        });
    }

    protected function getHashConnection(): string
    {
        return 'draw';
    }

    // The participant who organized the draw, null when the organizer doesn't take part
    // (still the first participant in the form and the panel, see DrawFormHandler)
    /** @return Attribute<Participant|null, never> */
    protected function organizer(): Attribute
    {
        return Attribute::make(get: function (): ?Participant {
            if ($this->organizer_participant_id !== null) {
                return $this->participants->firstWhere('id', $this->organizer_participant_id);
            }

            // Draws made before organizer_participant_id: the first participant, with the organizer's email
            // (can go once they are all pruned, a year and WEEKS_BEFORE_DELETION after the column was added)
            $first = $this->participants->first();

            return $first !== null && $first->email === $this->organizer_email ? $first : null;
        });
    }

    /** @return Attribute<bool, never> */
    protected function expired(): Attribute
    {
        return Attribute::make(get: fn (): bool => $this->expires_at->isPast());
    }

    /** @return Attribute<CarbonImmutable, never> */
    protected function deletedAt(): Attribute
    {
        return Attribute::make(get: fn (): CarbonImmutable => $this->expires_at->addWeeks(self::WEEKS_BEFORE_DELETION));
    }

    /** @return Attribute<string, never> */
    protected function metricId(): Attribute
    {
        return Attribute::make(get: fn (): string => hash('xxh32', (string) $this->created_at, false, ['seed' => $this->id]));
    }

    /** @return Attribute<bool, never> */
    protected function canRedraw(): Attribute
    {
        return Attribute::make(get: fn (): bool => DrawHandler::canRedraw($this->participants->redrawables()));
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'expires_at' => 'immutable_datetime',
            'targets_revealed_at' => 'immutable_datetime',
            'organizer_received_at' => 'immutable_datetime',
            'mail_title' => EncryptedString::class,
            'mail_body' => EncryptedString::class,
            'next_solvable' => 'boolean',
            'refused_emails' => 'array',
            'organizer_name' => EncryptedString::class,
            'organizer_email' => EncryptedString::class,
        ];
    }

    // One per key, current first: an address refused before an APP_KEY rotation stays refused (APP_PREVIOUS_KEYS)
    /** @return non-empty-list<string> */
    private function emailFingerprints(string $email): array
    {
        $keys = [config()->string('app.key'), ...array_filter(config()->array('app.previous_keys'), is_string(...))];

        return array_values(array_map(fn (string $key): string => hash_hmac('sha256', mb_strtolower(mb_trim($email)), $key.$this->id), $keys));
    }
}
