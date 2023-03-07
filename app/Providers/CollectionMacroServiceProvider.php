<?php

namespace App\Providers;

use App\Collections\Arr;
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

        /**
         * Get the first key from the collection passing the given truth test.
         *
         * @param  callable|null  $callback
         * @param  mixed  $default
         * @return mixed
         */
        Collection::macro('firstKey', function (callable $callback = null, $default = null) {
            return Arr::firstKey($this->items, $callback, $default);
        });
    }
}
