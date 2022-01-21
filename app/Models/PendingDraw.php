<?php

namespace App\Models;

use App\Casts\EncryptedString;
use App\Events\PendingDrawStatusUpdated;
use Illuminate\Broadcasting\BroadcastException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;

class PendingDraw extends Model
{
    use HasFactory, MassPrunable;

    protected const HOURS_BEFORE_DELETION = 24;

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
        'data' => EncryptedString::class,
        'organizer_email' => EncryptedString::class,
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
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function prunable()
    {
        return static::where(function ($query) {
            $query
                ->where('status', '=', self::STATE_CREATED)
                ->where('updated_at', '<=', (new DateTime('now'))->sub(new DateInterval('P'.self::HOURS_BEFORE_DELETION.'D')));
        })->orWhere('status', '=', self::STATE_STARTED);
    }

    /**
     * The draw was created but is not yet validated
     */
    public const STATE_CREATED = 'created';

    /**
     * The draw was validated and is processing
     */
    public const STATE_DRAWING = 'drawing';

    /**
     * The draw is fully processed and the participants can use the website
     */
    public const STATE_STARTED = 'started';

    public static $statuses = [
        self::STATE_CREATED,
        self::STATE_DRAWING,
        self::STATE_STARTED,
    ];

    public function markAsWaiting() : void {
        $this->updateStatus(self::STATE_CREATED);
    }

    public function markAsDrawing() : void {
        $this->updateStatus(self::STATE_DRAWING);
    }

    public function markAsStarted(Draw $draw) : void {
        $this->draw()->associate($draw);

        $this->updateStatus(self::STATE_STARTED);
    }

    public function isWaiting() : bool {
        return $this->status === self::STATE_CREATED;
    }

    public function updateStatus($status)
    {
        $this->status = $status;
        $this->save();

        try {
            PendingDrawStatusUpdated::dispatch($this);
        } catch(BroadcastException $e) {
            // Ignore exception
        }
    }

    public function draw()
    {
        return $this->belongsTo(Draw::class);
    }
}
