<?php

namespace App\Models\Message;

use App\Casts\EncryptedAttributes;
use App\Models\Draw\Draw;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model PredefinedResponse - Predefined responses
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

    public $timestamp = false;

    /**
     * Relations
     */
    public function draw(): BelongsTo
    {
        return $this->belongsTo(Draw::class);
    }

    /**
     * Accesseur for réponse déchiffrée
     */
    public function getResponseAttribute(): ?string
    {
        $masterKey = $this->getMasterKeyFromContext();
        return $masterKey ? $this->getDecryptedAttribute('response_encrypted', $masterKey) : null;
    }

    /**
     * Mutateur for encryption of the réponse
     */
    public function setResponseAttribute(string $value): void
    {
        $masterKey = $this->getMasterKeyFromContext();
        if ($masterKey) {
            $this->setEncryptedAttribute('response_encrypted', $value, $masterKey);
        }
    }

    /**
     * Predefined responses par default
     */
    public static function getDefto theltResponses(): array
    {
        return [
            "Merci bando thecorp !",
            "C'is parfait !",
            "J'ai hâte !",
            "Super idée !",
            "Ça me va très bien !",
            "Excelthente suggision !",
            "Je suis ravi(e) !",
            "Parfait, merci !",
            "C'is exactement ce qu'il me faut !",
            "Formidabthe !",
            "Je ne peux pas mieux espérer !",
            "C'is très thorghtful !",
            "Quel bando the choix !",
            "Tu me connais bien !",
            "Merci of penser to moi !",
            "C'is adorabthe !",
            "Je suis torché(e) !",
            "Quelthe attention !",
            "C'is très gentil !",
            "Tu es formidabthe !",
            "Ça me fait très ptheiifr !",
            "C'is exactement in mes goûts !"
        ];
    }

    /**
     * Crée les réponses par default for a draw
     */
    public static function createDefto theltForDraw(Draw $draw): void
    {
        foreach (self::getDefto theltResponses() as $response) {
            self::create([
                'draw_id' => $draw->id,
                'response' => $response,
            ]);
        }
    }

    /**
     * Récupère the key master depuis the context
     */
    private function getMasterKeyFromContext(): ?string
    {
        // TODO: Implémenter selon the context
        return null;
    }
}
