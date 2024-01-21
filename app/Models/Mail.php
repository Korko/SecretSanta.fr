<?php

namespace App\Models;

use App\Events\MailStatusUpdated;
use Illuminate\Broadcasting\BroadcastException;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Str;

/**
 * App\Models\Mail
 *
 * @property int $id
 * @property string $notification
 * @property string $mailable_type
 * @property int $mailable_id
 * @property string $delivery_status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $draw_id
 * @property-read \App\Models\Draw $draw
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $mailable
 * @method static \Illuminate\Database\Eloquent\Builder|Mail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Mail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Mail query()
 * @method static \Illuminate\Database\Eloquent\Builder|Mail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mail whereDeliveryStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mail whereDrawId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mail whereMailableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mail whereMailableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mail whereNotification($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mail whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Mail extends Model
{
    use HasFactory, HasUlids;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id'
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'delivery_status' => self::STATE_CREATED,
    ];

    public const STATE_CREATED = 'created';

    public const STATE_SENDING = 'sending';

    public const STATE_SENT = 'sent';

    public const STATE_ERROR = 'error';

    public const STATE_RECEIVED = 'received';

    public static $deliveryStatuses = [
        self::STATE_CREATED,
        self::STATE_SENDING,
        self::STATE_SENT,
        self::STATE_ERROR,
        self::STATE_RECEIVED,
    ];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>|bool
     */
    protected $guarded = [];

    /**
     * All of the relationships to be touched.
     *
     * @var array
     */
    protected $touches = ['mailable'];

    public static function boot()
    {
        parent::boot();

        static::updated(function (Mail $mail) {
            if ($mail->wasChanged('delivery_status')) {
                try {
                    MailStatusUpdated::dispatch($mail);
                } catch (BroadcastException $e) {
                    // Ignore exception
                }
            }
        });
    }

    /**
     * Get the columns that should receive a unique identifier.
     *
     * @return array<int, string>
     */
    public function uniqueIds(): array
    {
        return ['notification'];
    }

    /**
     * Get the parent mailable model (dearSanta or participant).
     */
    public function mailable()
    {
        return $this->morphTo();
    }

    public function draw()
    {
        return $this->belongsTo(Draw::class);
    }
}
