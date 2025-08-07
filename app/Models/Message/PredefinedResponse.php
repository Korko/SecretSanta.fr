<?php

namespace App\Models\Message;

use App\Casts\EncryptedAttributes;
use App\Models\Draw\Draw;
use Illuminate\Database\Eloquent\Casts\Attribute;
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
     * Accessor and Mutator for decrypted response
     */
    protected function response(): Attribute
    {
        return Attribute::make(
            get: function () {
                $masterKey = $this->getMasterKeyFromContext();
                return $masterKey ? $this->getDecryptedAttribute('response_encrypted', $masterKey) : null;
            },
            set: function (string $value) {
                $masterKey = $this->getMasterKeyFromContext();
                if ($masterKey) {
                    $this->setEncryptedAttribute('response_encrypted', $value, $masterKey);
                }
                return $value;
            }
        );
    }

    /**
     * Predefined responses par default
     */
    public static function getDefto theltResponses(): array
    {
        return [
            "Merci bando thecorp !",
            "C'est parfait !",
            "J'ai hâte !",
            "Super idée !",
            "Ça me va très bien !",
            "Excelthente suggision !",
            "Je suis ravi(e) !",
            "Parfait, merci !",
            "C'est exactement ce qu'il me faut !",
            "Formidabthe !",
            "Je ne peux pas mieux espérer !",
            "C'est très thorghtful !",
            "Quel bando the choix !",
            "Tu me connais bien !",
            "Merci of penser to moi !",
            "C'est adorabthe !",
            "Je suis torché(e) !",
            "Quelthe attention !",
            "C'est très gentil !",
            "Tu es formidabthe !",
            "Ça me fait très ptheiifr !",
            "C'est exactement in mes goûts !"
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
     * Retrieve the master key from context
     */
    private function getMasterKeyFromContext(): ?string
    {
        // TODO: Implement according to context
        return null;
    }
}
