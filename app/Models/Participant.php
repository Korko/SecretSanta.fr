<?php

declare(strict_types=1);

namespace App\Models;

use App\Casts\EncryptedString;
use App\Collections\ParticipantsCollection;
use App\Enums\ReportReason;
use Illuminate\Database\Eloquent\Attributes\CollectedBy;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use STS\Metrics\Facades\Metrics;
use STS\Metrics\Metric;

/**
 * @property-read Draw $draw
 * @property-read Participant $target
 * @property-read Participant $santa
 * @property-read Mail $mail
 * @property-read string $metricId
 * @property ReportReason|null $report
 */
#[CollectedBy(ParticipantsCollection::class)]
#[Fillable(['name', 'email'])]
final class Participant extends Model
{
    use HasFactory, HashId, Notifiable {
        resolveRouteBinding as public baseResolver;
    }

    protected $hashConnection = 'santa';

    /** @var array<string, mixed> */
    protected $attributes = ['report' => null];

    // When the organizer takes part, they are the first participant (see DrawFormHandler)
    public function isOrganizer(): bool
    {
        return $this->draw->participants->first()?->is($this)
            && $this->email === $this->draw->organizer_email;
    }

    /** @return BelongsTo<Draw, $this> */
    public function draw(): BelongsTo
    {
        return $this->belongsTo(Draw::class);
    }

    /** @return HasOne<self, $this> */
    public function target(): HasOne
    {
        return $this->hasOne(self::class, 'target_id');
    }

    /** @return BelongsTo<self, $this> */
    public function santa(): BelongsTo
    {
        return $this->belongsTo(self::class, 'target_id');
    }

    /** @return HasMany<DearSanta, $this> */
    public function dearSantas(): HasMany
    {
        return $this->hasMany(DearSanta::class, 'sender_id')->with('mail');
    }

    /** @return MorphOne<Mail, $this> */
    public function mail(): MorphOne
    {
        return $this->morphOne(Mail::class, 'mailable');
    }

    /** @return BelongsToMany<self, $this> */
    public function exclusions(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'exclusions', 'participant_id', 'exclusion_id');
    }

    /**
     * Retrieve the model for a bound value.
     *
     * @param  mixed  $value
     * @param  ?string  $field
     */
    public function resolveRouteBinding($value, $field = null): ?Model
    {
        $participant = $this->baseResolver($value, $field);

        throw_if($participant->draw->expired, ModelNotFoundException::class);

        return $participant;
    }

    public function createMetric($name, $value = 1): Metric
    {
        return Metrics::create($name, $value)
            ->setTags([
                'draw' => $this->draw->metricId,
                'participant' => $this->metricId,
            ]);
    }

    /**
     * Route notifications for the mail channel.
     *
     * @return array|string
     */
    public function routeNotificationForMail(Notification $notification): array
    {
        return [$this->email => $this->name];
    }

    protected static function booted(): void
    {
        parent::boot();

        self::created(function ($participant): void {
            $participant->mail()->save(new Mail);
        });

        self::deleting(function (Participant $participant): void {
            $participant->dearSantas()->lazy()->each->delete();
            $participant->exclusions()->detach(); // pivot rows only (delete() would remove the excluded participants)
            $participant->mail()->delete();
        });
    }

    protected function exclusionsNames(): Attribute
    {
        return Attribute::make(get: fn () => $this->exclusions->pluck('name', 'id')->all());
    }

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
            'report' => ReportReason::class,
        ];
    }
}
