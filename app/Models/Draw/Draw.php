<?php

namespace App\Models\Draw;

use App\Casts\EncryptedAttributes;
use App\Enums\DrawStatus;
use App\Models\Message\Message;
use App\Models\Message\PredefinedResponse;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * Draw Model - Draws/Lotteries
 */
class Draw extends Model
{
    use HasFactory, EncryptedAttributes;

    public DrawStatus $status {
        set(DrawStatus|string $value) {
            $enumValue = $value instanceof DrawStatus ? $value : DrawStatus::from($value);
            $this->attributes['status'] = $enumValue->value;
        }

        get => DrawStatus::from($this->attributes['status'] ?? 'draft');
    }


    protected $fillable = [
        'user_id',
        'uuid',
        'organizer_key_hash',
        'master_key_encrypted',
        'title_encrypted',
        'description_encrypted',
        'organizer_name_encrypted',
        'organizer_email_encrypted',
        'status',
        'registration_deadline',
        'auto_accept_participants',
        'allow_target_messages',
        'drawn_at',
        'revealed_at',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'status' => DrawStatus::class,
        'auto_accept_participants' => 'boolean',
        'allow_target_messages' => 'boolean',
        'registration_deadline' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'drawn_at' => 'datetime',
        'revealed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($draw) {
            if (empty($draw->uuid)) {
                $draw->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Relations
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function participants(): HasMany
    {
        return $this->hasMany(Participant::class);
    }

    public function acceptedParticipants(): HasMany
    {
        return $this->hasMany(Participant::class)->where('status', 'accepted');
    }

    public function exclusionGroups(): HasMany
    {
        return $this->hasMany(ExclusionGroup::class);
    }

    public function exclusions(): HasMany
    {
        return $this->hasMany(Exclusion::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function predefinedResponses(): HasMany
    {
        return $this->hasMany(PredefinedResponse::class);
    }

    public function history(): HasMany
    {
        return $this->hasMany(DrawHistory::class, 'parent_draw_id');
    }

    /**
     * Scopes
     */
    #[Scope]
    public function active(Builder $query)
    {
        return $query->whereNotIn('status', ['archived']);
    }

    #[Scope]
    public function byStatus(Builder $query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Accessors for encrypted data
     */
    protected function title(): Attribute
    {
        return Attribute::make(
            get: function () {
                $masterKey = $this->getMasterKeyFromContext();
                return $masterKey ? $this->getDecryptedAttribute('title_encrypted', $masterKey) : null;
            }
        );
    }

    protected function description(): Attribute
    {
        return Attribute::make(
            get: function () {
                $masterKey = $this->getMasterKeyFromContext();
                return $masterKey ? $this->getDecryptedAttribute('description_encrypted', $masterKey) : null;
            }
        );
    }

    protected function organizerName(): Attribute
    {
        return Attribute::make(
            get: function () {
                $masterKey = $this->getMasterKeyFromContext();
                return $masterKey ? $this->getDecryptedAttribute('organizer_name_encrypted', $masterKey) : null;
            }
        );
    }

    protected function organizerEmail(): Attribute
    {
        return Attribute::make(
            get: function () {
                $masterKey = $this->getMasterKeyFromContext();
                return $masterKey ? $this->getDecryptedAttribute('organizer_email_encrypted', $masterKey) : null;
            }
        );
    }

    /**
     * State methods
     */
    public function isDraft(): bool
    {
        return $this->status === DrawStatus::DRAFT;
    }

    public function isOpenForRegistration(): bool
    {
        return $this->status === DrawStatus::OPEN_REGISTRATION;
    }

    public function isDrawn(): bool
    {
        return $this->status->isComplete();
    }

    public function isRevealed(): bool
    {
        return $this->status === DrawStatus::REVEALED;
    }

    public function canAcceptRegistrations(): bool
    {
        return $this->status->canAcceptRegistrations() &&
               (!$this->registration_deadline || now()->isBefore($this->registration_deadline));
    }

    /**
     * Draw actions with transition validation
     */
    public function openRegistrations(): void
    {
        if (!$this->status->canTransitionTo(DrawStatus::OPEN_REGISTRATION)) {
            throw new \InvalidArgumentException('Cannot transition to open registration from current status');
        }
        $this->status = DrawStatus::OPEN_REGISTRATION;
        $this->save();
    }

    public function closeRegistrations(): void
    {
        if (!$this->status->canTransitionTo(DrawStatus::CLOSED_REGISTRATION)) {
            throw new \InvalidArgumentException('Cannot close registrations from current status');
        }
        $this->status = DrawStatus::CLOSED_REGISTRATION;
        $this->save();
    }

    public function markAsDrawn(): void
    {
        if (!$this->status->canTransitionTo(DrawStatus::DRAWN)) {
            throw new \InvalidArgumentException('Cannot mark as drawn from current status');
        }
        $this->status = DrawStatus::DRAWN;
        $this->drawn_at = now();
        $this->save();
    }

    public function reveal(): void
    {
        if (!$this->status->canTransitionTo(DrawStatus::REVEALED)) {
            throw new \InvalidArgumentException('Cannot reveal from current status');
        }
        $this->status = DrawStatus::REVEALED;
        $this->revealed_at = now();
        $this->save();
    }

    /**
     * Retrieve master key from context
     */
    private function getMasterKeyFromContext(): ?string
    {
        // TODO: Implement according to context (parameter, cache, etc.)
        return null;
    }
}
