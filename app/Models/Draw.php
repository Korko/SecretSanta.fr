<?php

namespace App\Models;

use App\Casts\DrawEncryptedString;
use App\Collections\ParticipantsCollection;
use App\Enums\DrawStatus;
use App\Events\DrawStatusUpdated;
use Illuminate\Broadcasting\BroadcastException;
use Illuminate\Contracts\Routing\UrlRoutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * App\Models\Draw
 *
 * @property int $id
 * @property mixed $title
 * @property mixed $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $finished_at
 * @property-read mixed $can_redraw
 * @property-read mixed $deletes_at
 * @property-read mixed $expires_at
 * @property-read mixed $hash
 * @property-read mixed $is_finished
 * @property-read mixed $metric_id
 * @property-read mixed $organizer
 * @property-read \App\Collections\ParticipantsCollection|\App\Models\Participant[] $participants
 * @property-read int|null $participants_count
 * @method static \Database\Factories\DrawFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Draw findByHashOrFail($hash)
 * @method static \Illuminate\Database\Eloquent\Builder|Draw newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Draw newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Draw query()
 * @method static \Illuminate\Database\Eloquent\Builder|Draw whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Draw whereFinishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Draw whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Draw whereMailBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Draw whereMailTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Draw whereNextSolvable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Draw whereOrganizerEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Draw whereOrganizerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Draw whereUpdatedAt($value)
 * @property string $ulid
 * @property bool $participant_organizer
 * @property int|null $organizer_id
 * @property string|null $budget
 * @property string|null $event_date
 * @property Carbon|null $ready_at
 * @property Carbon|null $drawn_at
 * @property DrawStatus $status
 * @property string $secret
 * @property-read ParticipantsCollection $santas
 * @method static Builder|Draw whereBudget($value)
 * @method static Builder|Draw whereDescription($value)
 * @method static Builder|Draw whereDrawnAt($value)
 * @method static Builder|Draw whereEventDate($value)
 * @method static Builder|Draw whereOrganizerId($value)
 * @method static Builder|Draw whereParticipantOrganizer($value)
 * @method static Builder|Draw whereReadyAt($value)
 * @method static Builder|Draw whereSecret($value)
 * @method static Builder|Draw whereStatus($value)
 * @method static Builder|Draw whereTitle($value)
 * @method static Builder|Draw whereUlid($value)
 * @mixin \Eloquent
 */
class Draw extends Model implements UrlRoutable
{
    use HasFactory, MassPrunable, HasUlids;

    // Keep a draw at least this amount of months after the creation, update or not
    public const MIN_MONTHS_BEFORE_EXPIRATION = 6;

    // Keep a draw at max this amount of months after the last update
    public const MONTHS_BEFORE_EXPIRATION_AFTER_START = 3;

    // Keep a draw at max this amount of days after the creation
    public const DAYS_BEFORE_DELETION_BEFORE_START = 30;

    // Keep a draw at max this amount of days after the creation in case of cleanup request
    public const DAYS_BEFORE_QUICK_DELETION = 7;

    // Keep a draw at max this amount of days after the creation in case of error
    public const DAYS_BEFORE_DELETION_BEFORE_CLEANUP = 30;

    // Remove everything N days after the expires_at or the finished_at date
    public const DAYS_BEFORE_DELETION_AFTER_FINISH = 7;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>|bool
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'status' => DrawStatus::class,
        'ready_at' => 'datetime',
        'drawn_at' => 'datetime',
        'finished_at' => 'datetime',
        'title' => DrawEncryptedString::class,
        'description' => DrawEncryptedString::class,
        'participant_organizer' => 'boolean',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'status' => DrawStatus::CREATED,
        'description' => null,
        'ready_at' => null,
        'drawn_at' => null,
        'finished_at' => null,
        'organizer_id' => null,
        'participant_organizer' => true,
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'expires_at',
        'deletes_at',
        'is_expired',
        'is_finished',
    ];

    protected static function booted(): void
    {
        static::updated(function (Draw $draw) {
            if ($draw->wasChanged('status')) {
                try {
                    DrawStatusUpdated::dispatch($draw);
                } catch (BroadcastException $e) {
                    // Ignore exception
                }
            }
        });
    }

    /**
     * Get the columns that should receive a unique identifier.
     *
     * @return array<int, string>
     */
    public function uniqueIds(): array
    {
        return ['ulid'];
    }

    public function prunable(): Builder
    {
        // Copy of deletes_at and expires_at cases, don't know yet how to merge them
        return static::where(function(Builder $query) {
                $query->where('status', DrawStatus::CREATED)
                    ->where('created_at', '<=', Carbon::now()->subDays(self::DAYS_BEFORE_DELETION_BEFORE_START));
            })
            ->orWhere(function(Builder $query) {
                $query->where('status', DrawStatus::CANCELED)
                    ->where('created_at', '<=', Carbon::now()->subDays(self::DAYS_BEFORE_QUICK_DELETION));
            })
            ->orWhere(function(Builder $query) {
                $query->where('status', DrawStatus::ERROR)
                    ->where('created_at', '<=', Carbon::now()->subDays(self::DAYS_BEFORE_DELETION_BEFORE_CLEANUP));
            })
            ->orWhere(function(Builder $query) {
                $query->where('status', DrawStatus::STARTED)
                    ->where('created_at', '>=', Carbon::now()->subMonths(self::MIN_MONTHS_BEFORE_EXPIRATION))
                    ->where('updated_at', '<=', Carbon::now()->subMonths(self::MONTHS_BEFORE_EXPIRATION_AFTER_START));
            })
            ->orWhere(function(Builder $query) {
                $query->where('status', DrawStatus::FINISHED)
                    ->where('finished_at', '<=', Carbon::now()->subDays(self::DAYS_BEFORE_DELETION_AFTER_FINISH));
            });
    }

    /**
     * Get all users, including organizer (participating or not)
     */
    public function participants(): HasMany
    {
        return $this->hasMany(Participant::class);
    }

    /**
     * Get all participants (including organizer if participating)
     */
    public function santas(): Attribute
    {
        return Attribute::make(
            get: fn (): ParticipantsCollection => $this->participant_organizer ?
                $this->participants->diff([$this->organizer]) :
                $this->participants
            );
    }

    /**
     * Get organizer
     */
    public function organizer(): BelongsTo
    {
        return $this->belongsTo(Participant::class);
    }

    /**
     * Get all participants but the organizer, even if participating
     */
    protected function santasNonOrganizer(): Attribute
    {
        return Attribute::make(
            get: fn (): ParticipantsCollection => $this->participants->diff([$this->organizer])
        );
    }

    protected function expiresAt(): Attribute
    {
        return Attribute::make(
            get: fn (): ?Carbon => match ($this->status) {
                DrawStatus::STARTED => max(
                    $this->created_at->addMonths(self::MIN_MONTHS_BEFORE_EXPIRATION),
                    $this->updated_at->addMonths(self::MONTHS_BEFORE_EXPIRATION_AFTER_START)
                ),
                default => null,
            }
        );
    }

    protected function deletesAt(): Attribute
    {
        return Attribute::make(
            get: fn (): Carbon => match ($this->status) {
                DrawStatus::CREATED => $this->created_at->addDays(self::DAYS_BEFORE_DELETION_BEFORE_START),
                DrawStatus::CANCELED => $this->created_at->addDays(self::DAYS_BEFORE_QUICK_DELETION),
                DrawStatus::ERROR => $this->created_at->addDays(self::DAYS_BEFORE_DELETION_BEFORE_CLEANUP),
                DrawStatus::STARTED => $this->expires_at->addDays(self::DAYS_BEFORE_DELETION_AFTER_FINISH),
                DrawStatus::FINISHED => $this->finished_at->addDays(self::DAYS_BEFORE_DELETION_AFTER_FINISH),
            }
        );
    }

    protected function isExpired(): Attribute
    {
        return Attribute::make(
            get: fn (): bool => $this->expired_at?->isPast() ?: false
        );
    }

    protected function isFinished(): Attribute
    {
        return Attribute::make(
            get: fn (): bool => $this->finished_at?->isPast() ?: false
        );
    }

    public function getRouteKeyName(): string
    {
        return 'ulid';
    }
}
