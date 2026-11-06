<?php

namespace App\Models;

use App\Casts\EncryptedString;
use App\Services\DrawHandler;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Date;
use STS\Metrics\Facades\Metrics;
use STS\Metrics\Metric;

#[Fillable(['mail_title', 'mail_body', 'expires_at', 'next_solvable', 'organizer_name', 'organizer_email'])]
final class Draw extends Model
{
    use HasFactory;
    use HashId;
    use Notifiable;
    use Prunable;

    // Remove everything N weeks after the expiration_date
    public const int WEEKS_BEFORE_DELETION = 3;

    protected $hashConnection = 'draw';

    public function save(array $options = [])
    {
        $this->expires_at = $this->expires_at ?: Date::now()->startOfDay()->addDays(7);

        return parent::save($options);
    }

    /**
     * Get the prunable model query.
     */
    public function prunable(): Builder
    {
        return self::query()->where('expires_at', '<=', Date::now()->subWeeks(self::WEEKS_BEFORE_DELETION));
    }

    public function participants(): HasMany
    {
        return $this->hasMany(Participant::class);
    }

    public function createMetric($name): Metric
    {
        return Metrics::create($name)
            ->setTags([
                'draw' => $this->metricId,
            ]);
    }

    /**
     * The attributes that should be mutated to dates.
     *
     * @var string[]
     */
    protected static function booted(): void
    {
        self::deleting(function ($draw): void {
            $draw->participants()->lazy()->each->delete();
        });
    }

    protected function organizer(): Attribute
    {
        return Attribute::make(get: fn () => $this->participants->first());
    }

    protected function expired(): Attribute
    {
        return Attribute::make(get: fn () => $this->expires_at->isPast());
    }

    protected function deletedAt(): Attribute
    {
        return Attribute::make(get: fn () => $this->expires_at->addWeeks(self::WEEKS_BEFORE_DELETION));
    }

    protected function metricId(): Attribute
    {
        return Attribute::make(get: fn (): string => hash('xxh32', $this->created_at, false, ['seed' => $this->id]));
    }

    protected function canRedraw(): Attribute
    {
        return Attribute::make(get: fn (): bool => DrawHandler::canRedraw($this->participants->redrawables()));
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'mail_title' => EncryptedString::class,
            'mail_body' => EncryptedString::class,
            'next_solvable' => 'boolean',
            'organizer_name' => EncryptedString::class,
            'organizer_email' => EncryptedString::class,
        ];
    }
}
