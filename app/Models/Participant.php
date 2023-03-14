<?php

namespace App\Models;

use App\Casts\DrawEncryptedString;
use App\Collections\ParticipantsCollection;
use exussum12\xxhash\V32 as xxHash;
use Illuminate\Contracts\Routing\UrlRoutable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Metrics;

/**
 * App\Models\Participant
 *
 * @property int $id
 * @property int $draw_id
 * @property mixed $name
 * @property mixed $email
 * @property int|null $target_id
 * @property int $redraw
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\DearSanta[] $dearSantas
 * @property-read int|null $dear_santas_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\DearTarget[] $dearTargets
 * @property-read int|null $dear_targets_count
 * @property-read \App\Models\Draw $draw
 * @property-read ParticipantsCollection|Participant[] $exclusions
 * @property-read int|null $exclusions_count
 * @property-read mixed $exclusions_names
 * @property-read mixed $hash
 * @property-read mixed $metric_id
 * @property-read \App\Models\Mail|null $mail
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read Participant|null $santa
 * @property-read Participant|null $target
 *
 * @method static ParticipantsCollection|static[] all($columns = ['*'])
 * @method static \Database\Factories\ParticipantFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Participant findByHashOrFail($hash)
 * @method static ParticipantsCollection|static[] get($columns = ['*'])
 * @method static \Illuminate\Database\Eloquent\Builder|Participant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Participant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Participant query()
 * @method static \Illuminate\Database\Eloquent\Builder|Participant whereDrawId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Participant whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Participant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Participant whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Participant whereRedraw($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Participant whereTargetId($value)
 * @mixin \Eloquent
 */
class Participant extends Model implements UrlRoutable
{
    use HasFactory, Notifiable, HasHash {
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
        'name' => DrawEncryptedString::class,
        'email' => DrawEncryptedString::class,
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * All of the relationships to be touched.
     *
     * @var array
     */
    protected $touches = ['draw'];

    protected static function booted()
    {
        parent::boot();

        static::created(function ($participant) {
            $mail = new Mail;
            $mail->draw()->associate($participant->draw);

            $participant->mail()->save($mail);
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

    public function dearTargets()
    {
        return $this->hasMany(DearTarget::class, 'sender_id')->with('mail');
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
     * @param mixed $value
     * @param string|null $field
     */
    public function resolveRouteBinding($value, $field = null): ?Model
    {
        $participant = $this->baseResolver($value, $field);
        $participant->load(['mail', 'draw', 'santa', 'target']);

        throw_if($participant->draw->isFinished, ModelExpiredException::class);

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
     */
    public function routeNotificationForMail(?Notification $notification): array|string
    {
        return [
            ['name' => $this->name, 'email' => $this->email],
        ];
    }

    /**
     * Get the queueable relationships for the entity.
     */
    public function getQueueableRelations(): array
    {
        // To prevent infinite loop with recursive references, we unset the relations list (exclusions, target)
        $this->unsetRelations();

        return parent::getQueueableRelations();
    }
}
