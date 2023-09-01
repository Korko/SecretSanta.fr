<?php

namespace App\Models;

use App\Casts\DrawEncryptedString;
use App\Enums\EmailAddressStatus;
use App\Enums\PendingDrawStatus;
use App\Events\PendingDrawStatusUpdated;
use App\Facades\DrawCrypt;
use App\Models\HasHash;
use App\Models\PendingParticipant;
use Carbon\Carbon;
use DateInterval;
use DateTime;
use Illuminate\Broadcasting\BroadcastException;
use Illuminate\Contracts\Routing\UrlRoutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
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
class PendingDraw extends Model implements UrlRoutable
{
    use HasFactory, MassPrunable, HasHash;

    protected $hashConnection = 'pendingDraw';

    protected $retentionDaysPerStatus = [
        (PendingDrawStatus::CREATED)->value => 30,
        (PendingDrawStatus::STARTED)->value => 6 * 30,
        (PendingDrawStatus::ERROR)->value => 3 * 30,
        (PendingDrawStatus::CANCELED)->value => 7
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'title' => DrawEncryptedString::class,
        'organizer_name' => DrawEncryptedString::class,
        'organizer_email' => DrawEncryptedString::class,
        'email_status' => EmailAddressStatus::class,
        'status' => PendingDrawStatus::class,
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'draw_id' => null,
        'organizer_id' => null,
        'email_status' => EmailAddressStatus::CREATED,
        'status' => PendingDrawStatus::CREATED,
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::updated(function (PendingDraw $pendingDraw) {
            if ($pendingDraw->wasChanged('status')) {
                try {
                    PendingDrawStatusUpdated::dispatch($pendingDraw);
                } catch (BroadcastException $e) {
                    // Ignore exception
                }
            }
        });
    }

    /**
     * Get the prunable model query.
     */
    public function prunable(): Builder
    {
        return static::where(function ($query) {
            foreach ($this->retentionDaysPerStatus as $status => $retention) {
                $query->orWhere(function ($query) use ($status, $retention) {
                    $query
                        ->where('status', '=', $status)
                        ->where('updated_at', '<=', Carbon::now()->subDays($retention));
                });
            }
        });
    }

    public function participants()
    {
        return $this->hasMany(PendingParticipant::class);
    }

    public function draw()
    {
        return $this->belongsTo(Draw::class);
    }

    public function organizer()
    {
        return $this->belongsTo(PendingParticipant::class)
            ->withDefault(function ($instance) {
                return new AnonymousNotifiable([
                    $instance->organizer_email => $instance->organizer_name,
                ]);
            });
    }

    protected function participantOrganizer(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->organizer instanceof PendingParticipant
        );
    }

    public function isEmailConfirmed() : bool
    {
        return $this->email_status === EmailAddressStatus::CONFIRMED;
    }
}
