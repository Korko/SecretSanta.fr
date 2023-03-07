<?php

namespace App\Models;

use App\Casts\DrawEncryptedString;
use App\Events\PendingDrawStatusUpdated;
use DateInterval;
use DateTime;
use Illuminate\Broadcasting\BroadcastException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\AnonymousNotifiable;

/**
 * App\Models\PendingDraw
 *
 * @property int $id
 * @property mixed $organizer_name
 * @property mixed $organizer_email
 * @property mixed|null $data
 * @property string $status
 * @property int|null $draw_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Draw|null $draw
 *
 * @method static \Database\Factories\PendingDrawFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|PendingDraw newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PendingDraw newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PendingDraw query()
 * @method static \Illuminate\Database\Eloquent\Builder|PendingDraw whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PendingDraw whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PendingDraw whereDrawId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PendingDraw whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PendingDraw whereOrganizerEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PendingDraw whereOrganizerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PendingDraw whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PendingDraw whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PendingDraw extends Model
{
    use HasFactory, MassPrunable;

    protected $retentionPerStatus = [
        self::STATE_CREATED => 24,
        self::STATE_STARTED => 24 * 7,
        self::STATE_ERROR => 24 * 7,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = ['data', 'status'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'data' => DrawEncryptedString::class,
        'organizer_name' => DrawEncryptedString::class,
        'organizer_email' => DrawEncryptedString::class,
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'status' => self::STATE_CREATED,
    ];

    /**
     * Get the prunable model query.
     */
    public function prunable(): Builder
    {
        return static::where(function ($query) {
            foreach ($this->retentionPerStatus as $status => $retention) {
                $query->orWhere(function ($query) use ($status, $retention) {
                    $query
                        ->where('status', '=', $status)
                        ->where('updated_at', '<=', (new DateTime('now'))->sub(new DateInterval('P'.$retention.'D')));
                });
            }
        });
    }

    /**
     * The draw was created but is not yet validated
     */
    public const STATE_CREATED = 'created';

    /**
     * The draw was validated and is ready to be processed
     */
    public const STATE_READY = 'ready';

    /**
     * The draw is processing
     */
    public const STATE_DRAWING = 'drawing';

    /**
     * The draw is fully processed and the participants can use the website
     */
    public const STATE_STARTED = 'started';

    /**
     * The draw is unsolvable and thus, cannot be processed
     */
    public const STATE_ERROR = 'error';

    public static $statuses = [
        self::STATE_CREATED,
        self::STATE_READY,
        self::STATE_DRAWING,
        self::STATE_STARTED,
        self::STATE_ERROR,
    ];

    public function markAsReady(): void
    {
        $this->updateStatus(self::STATE_READY);
    }

    public function markAsDrawing(): void
    {
        $this->updateStatus(self::STATE_DRAWING);
    }

    public function markAsStarted(Draw $draw): void
    {
        $this->data = null; // Dont keep data, the draw is already started
        $this->save();

        $this->draw()->associate($draw);

        $this->updateStatus(self::STATE_STARTED);
    }

    public function markAsUnsolvable(): void
    {
        $this->updateStatus(self::STATE_ERROR);
    }

    public function isWaiting(): bool
    {
        return $this->status === self::STATE_CREATED;
    }

    public function isReady(): bool
    {
        return $this->status === self::STATE_READY;
    }

    public function updateStatus($status)
    {
        $this->status = $status;
        $this->save();

        try {
            PendingDrawStatusUpdated::dispatch($this);
        } catch (BroadcastException $e) {
            // Ignore exception
        }
    }

    public function draw()
    {
        return $this->belongsTo(Draw::class);
    }

    public function getOrganizerAttribute()
    {
        return new AnonymousNotifiable([
            $this->organizer_email => $this->organizer_name,
        ]);
    }
}
