<?php

namespace App\Models;

use App\Casts\EncryptedString;
use App\Events\DrawDeleted;
use App\Services\DrawHandler;
use DateInterval;
use DateTime;
use exussum12\xxhash\V32 as xxHash;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Metrics;

class Draw extends Model
{
    use HasFactory, HashId, Notifiable;

    // Remove everything N weeks after the expiration_date
    const WEEKS_BEFORE_DELETION = 3;

    protected $hashConnection = 'draw';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['mail_title', 'mail_body', 'expires_at', 'next_solvable'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'mail_title' => EncryptedString::class,
        'mail_body' => EncryptedString::class,
        'next_solvable' => 'boolean',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'expires_at',
    ];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    protected static function booted()
    {
        static::deleting(function ($draw) {
            $draw->participants->each->delete();
        });
    }

    public function save(array $options = [])
    {
        $this->expires_at = $this->expires_at ?: (new DateTime('now'))->add(new DateInterval('P7D'));

        return parent::save($options);
    }

    public static function cleanup()
    {
        // Do not directly use ->delete() on the query to trigger the deleted event to cleanup the rest of the data
        //self::where('expires_at', '<=', (new DateTime('now'))->sub(new DateInterval('P'.(self::WEEKS_BEFORE_DELETION * 7).'D')))->get()
        //    ->each->delete();
    }

    public function participants()
    {
        return $this->hasMany(Participant::class)->with('mail');
    }

    public function getOrganizerAttribute()
    {
        return $this->participants->first();
    }

    public function getExpiredAttribute()
    {
        return $this->expires_at->isPast();
    }

    public function getDeletedAtAttribute()
    {
        return $this->expires_at->addWeeks(self::WEEKS_BEFORE_DELETION);
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
