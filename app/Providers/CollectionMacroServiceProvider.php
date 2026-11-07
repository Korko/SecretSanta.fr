<?php

declare(strict_types=1);

namespace App\Providers;

use Csv;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

final class CollectionMacroServiceProvider extends ServiceProvider
{
    /**
     * Register the application's response macros.
     */
    public function boot(): void
    {
        Collection::macro('toCsv', function (array $attributes): Csv {
            /** @var list<string> $attributes */
            return new Csv($this->map(fn (Model $model): array => array_map(function (string $attribute) use ($model): bool|float|int|string {
                $value = $model->getAttribute($attribute);

                return is_scalar($value) ? $value : implode(',', array_filter((array) $value, is_scalar(...)));
            }, $attributes))->values()->all());
        });
    }
}
