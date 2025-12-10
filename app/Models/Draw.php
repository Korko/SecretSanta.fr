<?php

namespace App\Models;

use App\Casts\EncryptedString;
use App\Services\DrawHandler;
use Carbon\Carbon;
use exussum12\xxhash\V32 as xxHash;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Metrics;

class Draw extends Model
{
    use HasFactory, HashId, Notifiable, Prunable;

    // Remove everything N weeks after the expiration_date
    public const WEEKS_BEFORE_DELETION = 3;

    protected $hashConnection = 'draw';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = ['mail_title', 'mail_body', 'expires_at', 'next_solvable', 'organizer_name', 'organizer_email'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'mail_title' => EncryptedString::class,
            'mail_body' => EncryptedString::class,
            'next_solvable' => 'boolean',
            'organizer_name' => EncryptedString::class,
            'organizer_email' => EncryptedString::class,
        ];
    }

    /**
     * The attributes that should be mutated to dates.
     *
     * @var string[]
     */
    protected static function booted()
    {
        static::deleting(function ($draw) {
            $draw->participants()->lazy()->each->delete();
        });
    }

    public function save(array $options = [])
    {
        $this->expires_at = $this->expires_at ?: Carbon::now()->startOfDay()->addDays(7);

        return parent::save($options);
    }

    /**
     * Get the prunable model query.
     */
    public function prunable(): Builder
    {
        return static::where('expires_at', '<=', Carbon::now()->subWeeks(self::WEEKS_BEFORE_DELETION));
    }

    public function participants(): HasMany
    {
        return $this->hasMany(Participant::class);
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
                'draw' => $this->metricId,
            ]);
    }
}
