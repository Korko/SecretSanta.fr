<?php

namespace App\Models;

use App\Casts\DrawEncryptedString;
use App\Enums\DrawStatus;
use App\Events\DrawStatusUpdated;
use Illuminate\Broadcasting\BroadcastException;
use Illuminate\Contracts\Routing\UrlRoutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Metrics;

/**
 * App\Models\Draw
 *
 * @property int $id
 * @property mixed $mail_title
 * @property mixed $mail_body
 * @property bool $next_solvable
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
 *
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
 * @mixin \Eloquent
 */
class Draw extends Model implements UrlRoutable
{
    use HasFactory, MassPrunable;

    // Keep a draw at least this amount of months after the creation, update or not
    public const MIN_MONTHS_BEFORE_EXPIRATION = 6;

    // Keep a draw at max this amount of months after the last update
    public const MONTHS_BEFORE_EXPIRATION = 3;

    // Remove everything N days after the finished_at date
    public const DAYS_BEFORE_DELETION = 7;

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

    protected static function booted(): void
    {
        static::creating(function (Draw $draw) {
            $draw->ulid = (string) Str::ulid();
        });

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

    public function prunable(): Builder
    {
        return static::where(function(Builder $query) {
                $query->where('status', DrawStatus::CREATED)
                    ->where('created_at', '<=', Carbon::now()->subDays(30));
            })
            ->orWhere(function(Builder $query) {
                $query->where('status', DrawStatus::CANCELED)
                    ->where('created_at', '<=', Carbon::now()->subDays(7));
            })
            ->orWhere(function(Builder $query) {
                $query->where('status', DrawStatus::ERROR)
                    ->where('created_at', '<=', Carbon::now()->subMonths(3));
            })
            ->orWhere(function(Builder $query) {
                $query->where('status', DrawStatus::STARTED)
                    ->where('created_at', '>=', Carbon::now()->subMonths(self::MIN_MONTHS_BEFORE_EXPIRATION))
                    ->where('updated_at', '<=', Carbon::now()->subMonths(self::MONTHS_BEFORE_EXPIRATION));
            })
            ->orWhere(function(Builder $query) {
                $query->where('status', DrawStatus::FINISHED)
                    ->where('finished_at', '<=', Carbon::now()->subDays(self::DAYS_BEFORE_DELETION));
            });
    }

    /**
     * Get all users, including organizer (participating or not)
     */
    public function users()
    {
        return $this->hasMany(Participant::class);
    }

    /**
     * Get all participants (including organizer if participating)
     */
    public function participants()
    {
        return Attribute::make(
            get: fn () => $this->participant_organizer ?
                $this->users->diff([$this->organizer]) :
                $this->users
            );
    }

    /**
     * Get organizer
     */
    public function organizer()
    {
        return $this->belongsTo(Participant::class);
    }

    /**
     * Get all participants but the organizer, even if participating
     */
    protected function participantsNonOrganizer(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->users->diff([$this->organizer])
        );
    }

    protected function expiresAt(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status === DrawStatus::STARTED ? max(
                $this->created_at->addMonths(self::MIN_MONTHS_BEFORE_EXPIRATION),
                $this->updated_at->addMonths(self::MONTHS_BEFORE_EXPIRATION)
            ) : null
        );
    }

    protected function deletesAt(): Attribute
    {
        return Attribute::make(
            get: fn () => max($this->expires_at, $this->finished_at?->addDays(self::DAYS_BEFORE_DELETION))
        );
    }

    protected function isExpired(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->expired_at?->isPast() ?: false
        );
    }

    protected function isFinished(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->finished_at?->isPast() ?: false
        );
    }

    public function getRouteKeyName()
    {
        return 'ulid';
    }
}
