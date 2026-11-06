<?php

namespace App\Providers;

use Csv;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\ServiceProvider;

final class CollectionMacroServiceProvider extends ServiceProvider
{
    /**
     * Register the application's response macros.
     */
    public function boot(): void
    {
        Collection::macro('toCsv', function (array $attributes): Csv {
            $attributes = collect($attributes);

            $collection = $this->map(fn ($model) => $attributes->mapWithKeys(function ($attribute) use ($model): array {
                $value = $model->$attribute;

                return [
                    $attribute => is_scalar($value) ? $value : implode(',', (array) $value),
                ];
            })->all());

            return new Csv($collection->all());
        });
    }
}
