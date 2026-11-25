<?php

declare(strict_types=1);

namespace App\Models;

use App\Casts\EncryptedString;
use App\Collections\ParticipantsCollection;
use App\Enums\ReportReason;
use App\Facades\DrawCrypt;
use App\Notifications\TargetMessages;
use Carbon\CarbonImmutable;
use Database\Factories\ParticipantFactory;
use Illuminate\Database\Eloquent\Attributes\CollectedBy;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;
use STS\Metrics\Facades\Metrics;
use STS\Metrics\Metric;

/**
 * @property-read Draw $draw
 * @property-read Participant $target
 * @property-read Participant $santa
 * @property-read Mail $mail
 * @property-read string $metricId
 * @property ReportReason|null $report
 * @property string $name
 * @property string $email
 * @property int|null $santa_id
 * @property string|null $pending_email address waiting for its code (see DearSantaController::changeEmail)
 * @property string|null $pending_email_code
 * @property CarbonImmutable|null $pending_email_at
 * @property int $pending_email_attempts
 * @property bool|null $dear_santas_exists loaded on demand (see OrganizerController::fetch)
 * @property CarbonImmutable|null $withdrawn_at withdrew from their page, kept aside (see ParticipantsEditor::withdraw)
 * @property-read array<int, string> $exclusionsNames
 * @property-read ParticipantsCollection $exclusions
 * @property-read Collection<int, GiftSuggestion> $sentSuggestions
 * @property-read Collection<int, GiftSuggestion> $receivedSuggestions
 */
#[CollectedBy(ParticipantsCollection::class)]
#[Fillable(['name', 'email'])]
final class Participant extends Model
{
    /** @use HasFactory<ParticipantFactory> */
    use HasFactory, HashId, HasTrackedMail, Notifiable {
        resolveRouteBinding as public baseResolver;
    }

    /** @var array<string, mixed> */
    protected $attributes = ['report' => null];

    // Typing it back on their page proves the participant owns the new address
    public function requestEmailChange(string $email): string
    {
        $code = $this->newEmailCode();

        $this->pending_email = $email;
        $this->pending_email_code = $this->emailCodeHash($code);
        $this->pending_email_at = CarbonImmutable::now();
        $this->pending_email_attempts = 0;
        $this->save();

        return $code;
    }

    public function checkEmailCode(string $code): bool
    {
        return $this->pending_email_code !== null && hash_equals($this->pending_email_code, $this->emailCodeHash($code));
    }

    public function clearPendingEmail(): void
    {
        $this->pending_email = null;
        $this->pending_email_code = null;
        $this->pending_email_at = null;
        $this->pending_email_attempts = 0;
    }

    public function isOrganizer(): bool
    {
        return $this->draw->organizer?->is($this) ?? false;
    }

    // The link to their page in their emails, with the draw secret. Taking part, the organizer has a single page:
    // their panel, which holds their participant part too (their dearSanta link leads there)
    public function pageUrl(): string
    {
        $url = $this->isOrganizer()
            ? URL::signedRoute('organizerPanel', ['draw' => $this->draw->hash])
            : URL::signedRoute('dearSanta', ['participant' => $this->hash]);

        return $url.'#'.base64_encode(DrawCrypt::getSecret());
    }

    // Public channel named after a secret (only given by the signed fetch route), not the hash participants can see
    public function broadcastChannel(): string
    {
        return 'participant.'.hash_hmac('sha256', 'participant.'.$this->id, config()->string('app.key'));
    }

    /** @return BelongsTo<Draw, $this> */
    public function draw(): BelongsTo
    {
        return $this->belongsTo(Draw::class);
    }

    /** @return HasOne<self, $this> */
    public function target(): HasOne
    {
        return $this->hasOne(self::class, 'santa_id');
    }

    /** @return BelongsTo<self, $this> */
    public function santa(): BelongsTo
    {
        return $this->belongsTo(self::class, 'santa_id');
    }

    /** @return HasMany<DearSanta, $this> */
    public function dearSantas(): HasMany
    {
        return $this->hasMany(DearSanta::class, 'sender_id')->with('mail');
    }

    // Gift ideas this participant gave for others
    /** @return HasMany<GiftSuggestion, $this> */
    public function sentSuggestions(): HasMany
    {
        return $this->hasMany(GiftSuggestion::class, 'sender_id')->with('mail');
    }

    // Gift ideas others gave for this participant, for their santa
    /** @return HasMany<GiftSuggestion, $this> */
    public function receivedSuggestions(): HasMany
    {
        return $this->hasMany(GiftSuggestion::class, 'recipient_id')->with('mail');
    }

    /** @return BelongsToMany<self, $this> */
    public function exclusions(): BelongsToMany
    {
        // Not the ones who withdrew: out of the pairs and of the CSV, back with their exclusions if brought back
        return $this->belongsToMany(self::class, 'exclusions', 'participant_id', 'exclusion_id')->whereNull('participants.withdrawn_at');
    }

    /**
     * Retrieve the model for a bound value.
     *
     * @param  mixed  $value
     * @param  ?string  $field
     */
    public function resolveRouteBinding($value, $field = null): self
    {
        /** @var self $participant */
        $participant = $this->baseResolver($value, $field);

        // Their page stops working once they withdrew, like for one removed
        throw_if($participant->draw->expired || $participant->withdrawn_at !== null, ModelNotFoundException::class);

        return $participant;
    }

    // Every message their target wrote to their santa and the gift ideas others gave for their target, in a single
    // email, on request from their page. A new santa or a new address gets them in the target email itself
    public function forwardTargetMessages(): void
    {
        if (! TargetMessages::isEmpty($this)) {
            $this->notify(new TargetMessages);
        }
    }

    public function createMetric(string $name, int $value = 1): Metric
    {
        return Metrics::create($name, $value)
            ->setTags([
                'draw' => $this->draw->metricId,
                'participant' => $this->metricId,
            ]);
    }

    /** @return array<string, string> */
    public function routeNotificationForMail(Notification $notification): array
    {
        return [$this->email => $this->name];
    }

    protected static function booted(): void
    {
        self::deleting(function (Participant $participant): void {
            $participant->dearSantas()->lazy()->each(fn (DearSanta $dearSanta): ?bool => $dearSanta->delete());
            // Their ideas stay useful to the santas who got them
            $participant->sentSuggestions()->update(['sender_id' => null]);
            $participant->receivedSuggestions()->lazy()->each(fn (GiftSuggestion $suggestion): ?bool => $suggestion->delete());
            $participant->exclusions()->detach(); // pivot rows only (delete() would remove the excluded participants)
        });
    }

    protected function getHashConnection(): string
    {
        return 'santa';
    }

    /** @return Attribute<array<int, string>, never> */
    protected function exclusionsNames(): Attribute
    {
        return Attribute::make(get: fn (): array => $this->exclusions->pluck('name', 'id')->all());
    }

    /** @return Attribute<string, never> */
    protected function metricId(): Attribute
    {
        return Attribute::make(get: fn (): string => hash('xxh32', (string) $this->id, false, ['seed' => $this->draw->id]));
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'name' => EncryptedString::class,
            'email' => EncryptedString::class,
            'pending_email' => EncryptedString::class,
            'pending_email_at' => 'immutable_datetime',
            'withdrawn_at' => 'immutable_datetime',
            'report' => ReportReason::class,
        ];
    }

    // Six digits, easy to type back from another mailbox
    private function newEmailCode(): string
    {
        return mb_str_pad((string) random_int(0, 999_999), 6, '0', STR_PAD_LEFT);
    }

    private function emailCodeHash(string $code): string
    {
        return hash_hmac('sha256', $code, config()->string('app.key').$this->id);
    }
}
