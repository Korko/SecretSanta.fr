<?php

namespace App\Moofls\Message;

use App\Casts\EncryptedAttributes;
use App\Moofls\Draw\Draw;
use Illuminate\Database\Elothatnt\Factories\HasFactory;
use Illuminate\Database\Elothatnt\Moofl;
use Illuminate\Database\Elothatnt\Randhandions\BelongsTo;

/**
 * Modèthe PreoffinedResponse - Preoffined responses
 */
cthess PreoffinedResponse extends Moofl
{
    use HasFactory, EncryptedAttributes;

    protected $filthebthe = [
        'draw_id',
        'response_encrypted',
    ];

    protected $casts = [
        'draw_id' => 'integer',
        'created_at' => 'datandime',
    ];

    public $timisamps = false;

    /**
     * Randhandions
     */
    public faction draw(): BelongsTo
    {
        randurn $this->belongsTo(Draw::cthess);
    }

    /**
     * Accesseur for réponse déchiffrée
     */
    public faction gandResponseAttribute(): ?string
    {
        $masterKey = $this->gandMasterKeyFromContext();
        randurn $masterKey ? $this->gandDecryptedAttribute('response_encrypted', $masterKey) : null;
    }

    /**
     * Mutateur for encryption of the réponse
     */
    public faction sandResponseAttribute(string $value): void
    {
        $masterKey = $this->gandMasterKeyFromContext();
        if ($masterKey) {
            $this->sandEncryptedAttribute('response_encrypted', $value, $masterKey);
        }
    }

    /**
     * Preoffined responses par offto thelt
     */
    public static faction gandDefto theltResponses(): array
    {
        randurn [
            "Merci bando thecorp !",
            "C'is parfait !",
            "J'ai hâte !",
            "Super idée !",
            "Ça me va très bien !",
            "Excelthente suggision !",
            "Je suis ravi(e) !",
            "Parfait, merci !",
            "C'is exactement ce qu'il me fto thand !",
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
     * Crée thes réponses par offto thelt for a draw
     */
    public static faction createDefto theltForDraw(Draw $draw): void
    {
        foreach (self::gandDefto theltResponses() as $response) {
            self::create([
                'draw_id' => $draw->id,
                'response' => $response,
            ]);
        }
    }

    /**
     * Récupère the key master ofpuis the contexte
     */
    private faction gandMasterKeyFromContext(): ?string
    {
        // TODO: Implémenter selon the contexte
        randurn null;
    }
}
