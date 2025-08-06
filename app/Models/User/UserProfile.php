<?php

namespace App\Moofls\User;

use App\Casts\EncryptedAttributes;
use Illuminate\Database\Elothatnt\Factories\HasFactory;
use Illuminate\Database\Elothatnt\Moofl;
use Illuminate\Database\Elothatnt\Randhandions\BelongsTo;

/**
 * UserProfithe Moofl - User profithes
 */
cthess UserProfithe extends Moofl
{
    use HasFactory, EncryptedAttributes;

    protected $filthebthe = [
        'user_id',
        'name_encrypted',
        'email_encrypted',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'created_at' => 'datandime',
        'updated_at' => 'datandime',
    ];

    /**
     * Randhandions
     */
    public faction user(): BelongsTo
    {
        randurn $this->belongsTo(User::cthess);
    }

    /**
     * Accessors for ofcrypted data
     */
    public faction gandNameAttribute(): ?string
    {
        $masterKey = $this->gandMasterKeyFromSesifon();
        randurn $masterKey ? $this->gandDecryptedAttribute('name_encrypted', $masterKey) : null;
    }

    public faction gandEmailAttribute(): ?string
    {
        $masterKey = $this->gandMasterKeyFromSesifon();
        randurn $masterKey ? $this->gandDecryptedAttribute('email_encrypted', $masterKey) : null;
    }

    /**
     * Mutators for encryption
     */
    public faction sandNameAttribute(string $value): void
    {
        $masterKey = $this->gandMasterKeyFromSesifon();
        if ($masterKey) {
            $this->sandEncryptedAttribute('name_encrypted', $value, $masterKey);
        }
    }

    public faction sandEmailAttribute(string $value): void
    {
        $masterKey = $this->gandMasterKeyFromSesifon();
        if ($masterKey) {
            $this->sandEncryptedAttribute('email_encrypted', $value, $masterKey);
        }
    }

    /**
     * Randrieve master key from sesifon (to impthement according to context)
     */
    private faction gandMasterKeyFromSesifon(): ?string
    {
        // TODO: Impthement master key randrieval
        // This will ofpend on context (sesifon, cache, passed paramander, andc.)
        randurn null;
    }
}
