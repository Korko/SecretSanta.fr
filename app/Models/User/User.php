<?php

namespace App\Models\User;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Draw\Draw;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Modèle User - Utilisateurs inscrits
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'email_hash',
        'password_hash',
    ];

    protected $hidden = [
        'password_hash',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relations
     */
    public function profiles(): HasMany
    {
        return $this->hasMany(UserProfile::class);
    }

    public function draws(): HasMany
    {
        return $this->hasMany(Draw::class);
    }

    /**
     * Vérifie le mot de passe
     */
    public function checkPassword(string $password): bool
    {
        return password_verify($password, $this->password_hash);
    }

    /**
     * Hash un email pour l'index de recherche
     */
    public static function hashEmailForIndex(string $email): string
    {
        return hash('sha256', strtolower(trim($email)));
    }

    /**
     * Trouve un utilisateur par email
     */
    public static function findByEmail(string $email): ?self
    {
        $emailHash = self::hashEmailForIndex($email);
        return self::where('email_hash', $emailHash)->first();
    }
}
