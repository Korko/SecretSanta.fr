<?php

namespace App\Models;

use App\Casts\EncryptedString;
use App\Collections\ParticipantsCollection;
use exussum12\xxhash\V32 as xxHash;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Metrics;

class Participant extends Model
{
    use HasFactory, Notifiable, HashId {
        resolveRouteBinding as public baseResolver;
    }

    protected $hashConnection = 'santa';

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'name' => EncryptedString::class,
        'email' => EncryptedString::class,
    ];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function draw()
    {
        return $this->belongsTo(Draw::class);
    }

    public function target()
    {
        return $this->hasOne(self::class, 'target_id');
    }

    public function santa()
    {
        return $this->belongsTo(self::class, 'target_id');
    }

    public function dearSanta()
    {
        return $this->hasMany(DearSanta::class, 'sender_id')->with('mail');
    }

    public function mail()
    {
        return $this->belongsTo(Mail::class, 'mail_id');
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

        abort_if($participant->draw->expired, 404);

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
        return (new xxHash($this->draw->id))->hash($this->id);
    }

    public function createMetric($name, $value = 1)
    {
        return Metrics::create($name, $value)
            ->setTags([
                'draw' => $this->draw->metricId,
                'participant' => $this->metricId,
                'is_organizer' => $this->is($this->draw->organizer)
            ]);
    }
}
