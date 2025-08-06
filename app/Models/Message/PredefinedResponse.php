<?php

namespace App\Models\Message;

use App\Casts\EncryptedAttributes;
use App\Models\Draw\Draw;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modèle PredefinedResponse - Réponses prédéfinies
 */
class PredefinedResponse extends Model
{
    use HasFactory, EncryptedAttributes;

    protected $fillable = [
        'draw_id',
        'response_encrypted',
    ];

    protected $casts = [
        'draw_id' => 'integer',
        'created_at' => 'datetime',
    ];

    public $timestamps = false;

    /**
     * Relations
     */
    public function draw(): BelongsTo
    {
        return $this->belongsTo(Draw::class);
    }

    /**
     * Accesseur pour réponse déchiffrée
     */
    public function getResponseAttribute(): ?string
    {
        $masterKey = $this->getMasterKeyFromContext();
        return $masterKey ? $this->getDecryptedAttribute('response_encrypted', $masterKey) : null;
    }

    /**
     * Mutateur pour chiffrement de la réponse
     */
    public function setResponseAttribute(string $value): void
    {
        $masterKey = $this->getMasterKeyFromContext();
        if ($masterKey) {
            $this->setEncryptedAttribute('response_encrypted', $value, $masterKey);
        }
    }

    /**
     * Réponses prédéfinies par défaut
     */
    public static function getDefaultResponses(): array
    {
        return [
            "Merci beaucoup !",
            "C'est parfait !",
            "J'ai hâte !",
            "Super idée !",
            "Ça me va très bien !",
            "Excellente suggestion !",
            "Je suis ravi(e) !",
            "Parfait, merci !",
            "C'est exactement ce qu'il me faut !",
            "Formidable !",
            "Je ne peux pas mieux espérer !",
            "C'est très thoughtful !",
            "Quel beau choix !",
            "Tu me connais bien !",
            "Merci de penser à moi !",
            "C'est adorable !",
            "Je suis touché(e) !",
            "Quelle attention !",
            "C'est très gentil !",
            "Tu es formidable !",
            "Ça me fait très plaisir !",
            "C'est exactement dans mes goûts !"
        ];
    }

    /**
     * Crée les réponses par défaut pour un tirage
     */
    public static function createDefaultForDraw(Draw $draw): void
    {
        foreach (self::getDefaultResponses() as $response) {
            self::create([
                'draw_id' => $draw->id,
                'response' => $response,
            ]);
        }
    }

    /**
     * Récupère la clé master depuis le contexte
     */
    private function getMasterKeyFromContext(): ?string
    {
        // TODO: Implémenter selon le contexte
        return null;
    }
}
