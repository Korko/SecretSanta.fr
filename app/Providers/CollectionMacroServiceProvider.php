<?php

namespace App\Providers;

use Csv;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\ServiceProvider;

class CollectionMacroServiceProvider extends ServiceProvider
{
    /**
     * Register the application's response macros.
     *
     * @return void
     */
    public function boot(): void
    {
        Collection::macro('toCsv', function (array $attributes) {
            $attributes = collect($attributes);

            $collection = $this->map(function ($model) use ($attributes) {
                return $attributes->mapWithKeys(function ($attribute) use ($model) {
                    $value = $model->$attribute;

                    return [
                        $attribute => is_scalar($value) ? $value : implode(',', (array) $value),
                    ];
                })->all();
            });

            return new Csv($collection->all());
        });
    }
}
