<?php

namespace App;

use App\Casts\EncryptedString;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Participant extends Model
{
    use Notifiable;
    use HashId {
        resolveRouteBinding as resolveHash;
    }

    protected static $hashConnection = 'santa';

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

    // Fake property
    public $exclusions;

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

    /**
     * Retrieve the model for a bound value.
     *
     * @param  mixed  $value
     * @param  string|null  $field
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        $participant = $field === 'hash' ?
            $this->resolveHash($value) :
            parent::resolveRouteBinding($value, $field);

        abort_if($participant === null || $participant->draw->expired, 404);

        return $participant;
    }
}
