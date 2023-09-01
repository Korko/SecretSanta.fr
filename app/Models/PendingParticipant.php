<?php

namespace App\Models;

use App\Casts\DrawEncryptedString;
use App\Enums\EmailAddressStatus;
use App\Models\HasHash;
use App\Models\PendingDraw;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;

class PendingParticipant extends Model
{
    use HasFactory, Notifiable, HasHash;

    protected $hashConnection = 'pendingParticipant';

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
        'email_status' => EmailAddressStatus::class,
        'exclusions' => 'array',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'email_status' => EmailAddressStatus::CREATED,
    ];

    public function pendingDraw()
    {
        return $this->belongsTo(PendingDraw::class);
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
}
