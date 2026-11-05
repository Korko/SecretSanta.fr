<?php

namespace App\Models\Draw;

use App\Casts\EncryptedAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;

/**
 * Organizer Model - Draw organizers (separate from participants)
 */
class Organizer extends Model
{
    use HasApiTokens, HasFactory, EncryptedAttributes;

    protected $fillable = [
        'draw_id',
        'uuid',
        'individual_key_hash',
        'master_key_encrypted',
        'name_encrypted',
        'email_encrypted',
        'email_sent_at',
        'email_opened_at',
        'last_access_at',
    ];

    protected $casts = [
        'draw_id' => 'integer',
        'email_sent_at' => 'datetime',
        'email_opened_at' => 'datetime',
        'last_access_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($organizer) {
            if (empty($organizer->uuid)) {
                $organizer->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Relations
     */
    public function draw(): BelongsTo
    {
        return $this->belongsTo(Draw::class);
    }

    public function emailDeliveries(): HasMany
    {
        return $this->hasMany(\App\Models\Email\EmailDelivery::class, 'recipient_id')
            ->where('recipient_type', self::class);
    }

    /**
     * Encrypted attributes accessors
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: function () {
                $masterKey = $this->getMasterKeyFromContext();
                return $masterKey ? $this->getDecryptedAttribute('name_encrypted', $masterKey) : null;
            },
            set: function (string $value) {
                $masterKey = $this->getMasterKeyFromContext();
                if ($masterKey) {
                    $this->setEncryptedAttribute('name_encrypted', $value, $masterKey);
                }
                return $value;
            }
        );
    }

    protected function email(): Attribute
    {
        return Attribute::make(
            get: function () {
                $masterKey = $this->getMasterKeyFromContext();
                return $masterKey ? $this->getDecryptedAttribute('email_encrypted', $masterKey) : null;
            },
            set: function (string $value) {
                $masterKey = $this->getMasterKeyFromContext();
                if ($masterKey) {
                    $this->setEncryptedAttribute('email_encrypted', $value, $masterKey);
                }
                return $value;
            }
        );
    }

    /**
     * Check if organizer has received their email
     */
    public function hasReceivedEmail(): bool
    {
        return !is_null($this->email_sent_at);
    }

    /**
     * Check if organizer has opened their email
     */
    public function hasOpenedEmail(): bool
    {
        return !is_null($this->email_opened_at);
    }

    /**
     * Mark email as sent
     */
    public function markEmailAsSent(): void
    {
        $this->update(['email_sent_at' => now()]);
    }

    /**
     * Mark email as opened
     */
    public function markEmailAsOpened(): void
    {
        $this->update(['email_opened_at' => now()]);
    }

    /**
     * Update last access time
     */
    public function updateLastAccess(): void
    {
        $this->update(['last_access_at' => now()]);
    }

    /**
     * Get the master key from context
     */
    private function getMasterKeyFromContext(): ?string
    {
        // TODO: Implement according to context
        return null;
    }
}