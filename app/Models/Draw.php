<?php

declare(strict_types=1);

namespace App\Models;

use App\Casts\EncryptedString;
use App\Collections\ParticipantsCollection;
use App\Enums\DeliveryStatus;
use App\Services\DrawHandler;
use Carbon\CarbonImmutable;
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
 * @property-read CarbonImmutable $deleted_at
 * @property CarbonImmutable|null $organizer_received_at
 * @property-read Participant $organizer
 * @property-read bool $expired
 * @property-read ParticipantsCollection $participants
 * @property list<string>|null $refused_emails
 */
#[Fillable(['mail_title', 'mail_body', 'expires_at', 'next_solvable', 'organizer_name', 'organizer_email'])]
final class Draw extends Model
{
    use HasFactory;
    use HashId;
    use Notifiable;
    use Prunable;

    // Remove everything N weeks after the expiration_date
    public const int WEEKS_BEFORE_DELETION = 3;

    protected $hashConnection = 'draw';

    public function save(array $options = [])
    {
        $this->expires_at = $this->expires_at ?: Date::now()->startOfDay()->addDays(7);

        return parent::save($options);
    }

    /**
     * Get the prunable model query.
     */
    public function prunable(): Builder
    {
        return self::query()->where('expires_at', '<=', Date::now()->subWeeks(self::WEEKS_BEFORE_DELETION));
    }

    /** @return HasMany<Participant, $this> */
    public function participants(): HasMany
    {
        return $this->hasMany(Participant::class);
    }

    public function createMetric($name): Metric
    {
        return Metrics::create($name)
            ->setTags([
                'draw' => $this->metricId,
            ]);
    }

    // Participants and exclusions, reusable to prefill the form next time (organizer panel + recap email)
    public function initialCsv(): string
    {
        return "\xEF\xBB\xBF".// UTF-8 BOM
            $this->participants
                ->toCsv(['name', 'email', 'exclusionsNames'])
                ->prepend([
                    ['# Fichier généré le '.date('d-m-Y').' sur '.config('app.name').' ('.config('app.url').')'],
                    ['# Ce fichier peut être utilisé pour préremplir les participants ainsi que les exclusions associées'],
                ]);
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
            || ($this->organizer->email === $this->organizer_email && $this->organizer->mail->delivery_status === DeliveryStatus::Received);
    }

    public function refusesEmail(string $email): bool
    {
        return in_array($this->emailFingerprint($email), $this->refused_emails ?? [], true);
    }

    public function refuseEmail(string $email, bool $refused = true): void
    {
        $fingerprints = array_diff($this->refused_emails ?? [], [$this->emailFingerprint($email)]);
        $this->refused_emails = array_values($refused ? [...$fingerprints, $this->emailFingerprint($email)] : $fingerprints);
        $this->save();
    }

    /**
     * The attributes that should be mutated to dates.
     *
     * @var string[]
     */
    protected static function booted(): void
    {
        self::deleting(function ($draw): void {
            $draw->participants()->lazy()->each->delete();
        });
    }

    protected function organizer(): Attribute
    {
        return Attribute::make(get: fn () => $this->participants->first());
    }

    protected function expired(): Attribute
    {
        return Attribute::make(get: fn () => $this->expires_at->isPast());
    }

    protected function deletedAt(): Attribute
    {
        return Attribute::make(get: fn () => $this->expires_at->addWeeks(self::WEEKS_BEFORE_DELETION));
    }

    protected function metricId(): Attribute
    {
        return Attribute::make(get: fn (): string => hash('xxh32', (string) $this->created_at, false, ['seed' => $this->id]));
    }

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
            'expires_at' => 'datetime',
            'targets_revealed_at' => 'datetime',
            'organizer_received_at' => 'datetime',
            'mail_title' => EncryptedString::class,
            'mail_body' => EncryptedString::class,
            'next_solvable' => 'boolean',
            'refused_emails' => 'array',
            'organizer_name' => EncryptedString::class,
            'organizer_email' => EncryptedString::class,
        ];
    }

    private function emailFingerprint(string $email): string
    {
        return hash_hmac('sha256', mb_strtolower(mb_trim($email)), config()->string('app.key').$this->id);
    }
}
