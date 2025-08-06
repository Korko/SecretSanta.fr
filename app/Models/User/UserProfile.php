<?php

namespace App\Models\User;

use App\Casts\EncryptedAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modèle UserProfile - Profils des utilisateurs
 */
class UserProfile extends Model
{
    use HasFactory, EncryptedAttributes;

    protected $fillable = [
        'user_id',
        'name_encrypted',
        'email_encrypted',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relations
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Accesseurs pour données déchiffrées
     */
    public function getNameAttribute(): ?string
    {
        $masterKey = $this->getMasterKeyFromSession();
        return $masterKey ? $this->getDecryptedAttribute('name_encrypted', $masterKey) : null;
    }

    public function getEmailAttribute(): ?string
    {
        $masterKey = $this->getMasterKeyFromSession();
        return $masterKey ? $this->getDecryptedAttribute('email_encrypted', $masterKey) : null;
    }

    /**
     * Mutateurs pour chiffrement
     */
    public function setNameAttribute(string $value): void
    {
        $masterKey = $this->getMasterKeyFromSession();
        if ($masterKey) {
            $this->setEncryptedAttribute('name_encrypted', $value, $masterKey);
        }
    }

    public function setEmailAttribute(string $value): void
    {
        $masterKey = $this->getMasterKeyFromSession();
        if ($masterKey) {
            $this->setEncryptedAttribute('email_encrypted', $value, $masterKey);
        }
    }

    /**
     * Récupère la clé master depuis la session (à implémenter selon le contexte)
     */
    private function getMasterKeyFromSession(): ?string
    {
        // TODO: Implémenter la récupération de la clé master
        // Cela dépendra du contexte (session, cache, paramètre passé, etc.)
        return null;
    }
}
