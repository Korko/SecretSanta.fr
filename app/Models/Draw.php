<?php

namespace App\Models;

use App\Casts\EncryptedString;
use App\Services\DrawHandler;
use DateInterval;
use DateTime;
use exussum12\xxhash\V32 as xxHash;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\MassPrunable;
use Metrics;

/**
 * App\Models\Draw
 *
 * @property int $id
 * @property mixed $organizer_name
 * @property mixed $organizer_email
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
class Draw extends Model
{
    use HasFactory, HasHash, MassPrunable;

    // Consider a draw expired N months after the last mail sent
    public const MONTHS_BEFORE_EXPIRATION = 3;

    // Remove everything N days after the finished_at date
    public const DAYS_BEFORE_DELETION = 7;

    protected $hashConnection = 'draw';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = ['mail_title', 'mail_body', 'next_solvable', 'organizer_name', 'organizer_email', 'created_at', 'updated_at', 'finished_at'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'mail_title' => EncryptedString::class,
        'mail_body' => EncryptedString::class,
        'next_solvable' => 'boolean',
        'organizer_name' => EncryptedString::class,
        'organizer_email' => EncryptedString::class,
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var string[]
     */
    protected $dates = [
        'finished_at',
    ];

    /**
     * Get the prunable model query.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function prunable()
    {
        return static::where('finished_at', '<=', (new DateTime('now'))->sub(new DateInterval('P'.self::DAYS_BEFORE_DELETION.'D')))
            ->orWhere('updated_at', '<=', (new DateTime('now'))->sub(new DateInterval('P'.self::MONTHS_BEFORE_EXPIRATION.'M')));
    }

    public function participants()
    {
        return $this->hasMany(Participant::class);
    }

    public function getOrganizerAttribute()
    {
        return $this->participants->first();
    }

    public function getExpiresAtAttribute()
    {
        return $this->updated_at->addMonths(self::MONTHS_BEFORE_EXPIRATION);
    }

    public function getIsFinishedAttribute()
    {
        return $this->finished_at ? $this->finished_at->isPast() : false;
    }

    public function getDeletesAtAttribute()
    {
        return max($this->expires_at, $this->finished_at ? $this->finished_at->addMonths(self::DAYS_BEFORE_DELETION) : null);
    }

    public function getMetricIdAttribute()
    {
        return (new xxHash($this->id))->hash($this->created_at);
    }

    public function getCanRedrawAttribute()
    {
        return DrawHandler::canRedraw($this->participants->redrawables());
    }

    public function createMetric($name)
    {
        return Metrics::create($name)
            ->setTags([
                'draw' => $this->metricId,
            ]);
    }
}
