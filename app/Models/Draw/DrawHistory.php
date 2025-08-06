<?php

namespace App\Models\Draw;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modèle DrawHistory - Historique des tirages précédents
 */
class DrawHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_draw_id',
        'edition_number',
        'assignments_data',
    ];

    protected $casts = [
        'parent_draw_id' => 'integer',
        'edition_number' => 'integer',
        'assignments_data' => 'array',
        'created_at' => 'datetime',
    ];

    public $timestamps = false;

    /**
     * Relations
     */
    public function parentDraw(): BelongsTo
    {
        return $this->belongsTo(Draw::class, 'parent_draw_id');
    }

    /**
     * Ajoute un historique d'assignations
     */
    public static function addAssignments(Draw $draw, array $assignments): self
    {
        $lastEdition = self::where('parent_draw_id', $draw->id)
            ->max('edition_number') ?? 0;

        return self::create([
            'parent_draw_id' => $draw->id,
            'edition_number' => $lastEdition + 1,
            'assignments_data' => $assignments,
        ]);
    }

    /**
     * Récupère tous les anciens appariements pour éviter les répétitions
     */
    public static function getPreviousAssignments(Draw $draw): array
    {
        return self::where('parent_draw_id', $draw->id)
            ->orderBy('edition_number', 'desc')
            ->get()
            ->pluck('assignments_data')
            ->flatten(1)
            ->unique()
            ->toArray();
    }

    /**
     * Convertit les assignations en exclusions
     */
    public function createExclusionsFromHistory(): void
    {
        foreach ($this->assignments_data as $assignment) {
            Exclusion::firstOrCreate([
                'draw_id' => $this->parent_draw_id,
                'participant_id' => $assignment['giver_id'],
                'excluded_participant_id' => $assignment['receiver_id'],
            ], [
                'type' => 'weak', // Les exclusions historiques sont faibles
                'source' => 'history',
            ]);
        }
    }
}
