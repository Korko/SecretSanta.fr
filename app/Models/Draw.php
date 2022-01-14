<?php

namespace App\Models;

use App\Casts\EncryptedString;
use App\Events\DrawDeleted;
use App\Services\DrawHandler;
use DateInterval;
use DateTime;
use exussum12\xxhash\V32 as xxHash;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Prunable;
use Metrics;

class Draw extends Model
{
    use HasFactory, HashId, Prunable;

    // Consider a draw expired N months after the last mail sent
    public const MONTHS_BEFORE_EXPIRATION = 3;
    // Remove everything N days after the expirated_at date
    public const DAYS_BEFORE_DELETION = 7;

    protected $hashConnection = 'draw';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = ['mail_title', 'mail_body', 'next_solvable', 'organizer_name', 'organizer_email', 'created_at', 'updated_at', 'expired_at'];

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
        'expired_at',
    ];

    protected static function booted()
    {
        static::deleting(function ($draw) {
            $draw->participants()->lazy()->each->delete();
        });
    }

    /**
     * Get the prunable model query.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function prunable()
    {
        return static::where('expired_at', '<=', (new DateTime('now'))->sub(new DateInterval('P'.self::DAYS_BEFORE_DELETION.'D')))
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

    public function getExpiredAttribute()
    {
        return $this->expired_at ? $this->expired_at->isPast() : false;
    }

    public function getDeletesAtAttribute()
    {
        return max($this->expires_at, $this->expired_at ? $this->expired_at->addMonths(self::DAYS_BEFORE_DELETION) : null);
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
                'draw' => $this->metricId
            ]);
    }
}
