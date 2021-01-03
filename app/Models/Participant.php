<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Casts\EncryptedString;
use App\Collections\ParticipantsCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Participant extends Model
{
    use HasFactory;

    use Notifiable;
    use HashId {
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
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'draw_id', 'target_id', 'mail_id'];

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

    public function scopeFindByDearSantaUrlOrFail($query, $url)
    {
        $route = app('router')
            ->getRoutes()
            ->getByName('dearsanta');

        $request = app('request')
            ->create($url);

        $hash = $route->bind($request)->participant;

        return $query->findByHashOrFail($hash);
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
}
