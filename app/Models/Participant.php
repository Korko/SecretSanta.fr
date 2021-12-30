<?php

namespace App\Models;

use App\Casts\EncryptedString;
use App\Collections\ParticipantsCollection;
use exussum12\xxhash\V32 as xxHash;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Notifications\Notifiable;
use Metrics;

class Participant extends Model
{
    use HasFactory, Notifiable, HashId {
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

        static::created(function($participant) {
            $participant->mail()->save(new Mail);
        });

        static::deleting(function ($participant) {
            $participant->dearSantas->each->delete();
            $participant->exclusions->each->delete();
            $participant->mail->delete();
        });
    }

    public function draw()
    {
        return $this->belongsTo(Draw::class);
    }

    public function target()
    {
        return $this->belongsTo(self::class, 'target_id');
    }

    public function santa()
    {
        return $this->hasOne(self::class, 'target_id');
    }

    public function dearSantas()
    {
        return $this->hasMany(DearSanta::class, 'sender_id')->with('mail');
    }

    public function mail()
    {
        return $this->morphOne(Mail::class, 'mailable');
    }

    public function exclusions()
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
     * @param  string|null  $field
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        $participant = $this->baseResolver($value, $field);

        throw_if($participant->draw->expired, ModelNotFoundException::class);

        return $participant;
    }

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array  $models
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function newCollection(array $models = [])
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
                'participant' => $this->metricId
            ]);
    }

    /**
     * Route notifications for the mail channel.
     *
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return array|string
     */
    public function routeNotificationForMail($notification)
    {
        return [$this->email => $this->name];
    }

    /**
     * Get the queueable relationships for the entity.
     *
     * @return array
     */
    public function getQueueableRelations()
    {
        // To prevent infinite loop with recursive references, we unset the relations list (exclusions, target)
        $this->unsetRelations();
        return parent::getQueueableRelations();
    }
}
