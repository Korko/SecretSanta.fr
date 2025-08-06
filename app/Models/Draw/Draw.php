<?php

namespace App\Models\Draw;

use App\Casts\EncryptedAttributes;
use App\Models\Message\Message;
use App\Models\Message\Message\Message;
use App\Models\Message\Message\PredefinedResponse;
use App\Models\Message\PredefinedResponse;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * Modèle Draw - Tirages au sort
 */
class Draw extends Model
{
    use HasFactory, EncryptedAttributes;

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
    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['archived']);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Accesseurs pour données déchiffrées
     */
    public function getTitleAttribute(): ?string
    {
        $masterKey = $this->getMasterKeyFromContext();
        return $masterKey ? $this->getDecryptedAttribute('title_encrypted', $masterKey) : null;
    }

    public function getDescriptionAttribute(): ?string
    {
        $masterKey = $this->getMasterKeyFromContext();
        return $masterKey ? $this->getDecryptedAttribute('description_encrypted', $masterKey) : null;
    }

    public function getOrganizerNameAttribute(): ?string
    {
        $masterKey = $this->getMasterKeyFromContext();
        return $masterKey ? $this->getDecryptedAttribute('organizer_name_encrypted', $masterKey) : null;
    }

    public function getOrganizerEmailAttribute(): ?string
    {
        $masterKey = $this->getMasterKeyFromContext();
        return $masterKey ? $this->getDecryptedAttribute('organizer_email_encrypted', $masterKey) : null;
    }

    /**
     * Méthodes d'état
     */
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isOpenForRegistration(): bool
    {
        return $this->status === 'open_registration';
    }

    public function isDrawn(): bool
    {
        return in_array($this->status, ['drawn', 'revealed']);
    }

    public function isRevealed(): bool
    {
        return $this->status === 'revealed';
    }

    public function canAcceptRegistrations(): bool
    {
        if (!$this->isOpenForRegistration()) {
            return false;
        }

        if ($this->registration_deadline && now()->isAfter($this->registration_deadline)) {
            return false;
        }

        return true;
    }

    /**
     * Actions sur le tirage
     */
    public function openRegistrations(): void
    {
        $this->update(['status' => 'open_registration']);
    }

    public function closeRegistrations(): void
    {
        $this->update(['status' => 'closed_registration']);
    }

    public function markAsDrawn(): void
    {
        $this->update([
            'status' => 'drawn',
            'drawn_at' => now(),
        ]);
    }

    public function reveal(): void
    {
        $this->update([
            'status' => 'revealed',
            'revealed_at' => now(),
        ]);
    }

    /**
     * Récupère la clé master depuis le contexte
     */
    private function getMasterKeyFromContext(): ?string
    {
        // TODO: Implémenter selon le contexte (paramètre, cache, etc.)
        return null;
    }
}
