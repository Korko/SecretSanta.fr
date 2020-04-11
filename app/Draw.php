<?php

namespace App;

use App\Casts\EncryptedString;
use DateInterval;
use DateTime;
use DB;
use Illuminate\Database\Eloquent\Model;

class Draw extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['mail_title', 'mail_body', 'expires_at'];

    protected $dates = [
        'expires_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'mail_title' => EncryptedString::class,
        'mail_body' => EncryptedString::class,
    ];

    public function save(array $options = [])
    {
        $this->expires_at = $this->expires_at ?: (new DateTime('now'))->add(new DateInterval('P7D'));

        return parent::save($options);
    }

    public static function cleanup()
    {
        self::where('expires_at', '<=', DB::raw('CURRENT_TIMESTAMP'))->delete();
    }

    public function participants()
    {
        return $this->hasMany(Participant::class)->with('mail');
    }

    public function getOrganizerAttribute()
    {
        return $this->participants->first();
    }

    public function getDrawFromDearSantaUrl($url)
    {
        $route = app('router')
            ->getRoutes()
            ->getByName('dearsanta');

        $request = app('request')
            ->create($this->argument('url'));

        $hash = $route->bind($request)->santa;
        if (!$hash || ! ($ids = Hashids::decode($hash))) {
            throw (new ModelNotFoundException())->setModel(Participant::class);
        }

        return Participant::findOrFail($id[0])->draw;
    }
}
