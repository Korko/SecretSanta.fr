<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Casts\EncryptedString;
use App\Collections\ParticipantsCollection;
use exussum12\xxhash\V32 as xxHash;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Metrics;

class Participant extends Model
{
    use HasFactory, HashId, Notifiable {
        resolveRouteBinding as public baseResolver;
    }

    protected $hashConnection = 'santa';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = ['name', 'email'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'name' => EncryptedString::class,
        'email' => EncryptedString::class,
    ];

    protected static function booted()
    {
        parent::boot();

        static::created(function ($participant) {
            $participant->mail()->save(new Mail);
        });

        static::deleting(function ($participant) {
            $participant->dearSantas()->lazy()->each->delete();
            $participant->exclusions()->delete();
            $participant->mail()->delete();
        });
    }

    public function draw(): BelongsTo
    {
        return $this->belongsTo(Draw::class);
    }

    public function target(): HasOne
    {
        return $this->hasOne(self::class, 'target_id');
    }

    public function santa(): BelongsTo
    {
        return $this->belongsTo(self::class, 'target_id');
    }

    public function dearSantas(): HasMany
    {
        return $this->hasMany(DearSanta::class, 'sender_id')->with('mail');
    }

    public function mail()
    {
        return $this->morphOne(Mail::class, 'mailable');
    }

    public function exclusions(): BelongsToMany
    {
        return $this->belongsToMany(Participant::class, 'exclusions', 'participant_id', 'exclusion_id');
    }

    public function getExclusionsNamesAttribute()
    {
        return $this->exclusions->pluck('name', 'id')->all();
    }

    /**
     * Retrieve the model for a bound value.
     *
     * @param  mixed  $value
     */
    public function resolveRouteBinding($value, ?string $field = null): ?Model
    {
        $participant = $this->baseResolver($value, $field);

        throw_if($participant->draw->expired, ModelNotFoundException::class);

        return $participant;
    }

    /**
     * Create a new Eloquent Collection instance.
     */
    public function newCollection(array $models = []): Collection
    {
        return new ParticipantsCollection($models);
    }

    public function getMetricIdAttribute()
    {
        return (new xxHash($this->draw->id))->hash((string) $this->id);
    }

    public function createMetric($name, $value = 1)
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
    public function routeNotificationForMail(Notification $notification)
    {
        return [$this->email => $this->name];
    }
}
