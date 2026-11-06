<?php

namespace App\Providers;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

final class ValidatorServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Laravel 11+ ships its own array "contains" rule, which shadows korko/validator's string one
        Validator::extend('contains_text', fn ($attribute, $value, $parameters): bool => is_string($value) && collect($parameters)->every(fn ($find): bool => str_contains($value, $find)));

        Validator::extend('in_keys', fn ($attribute, $value, $parameters, $validator) => collect($parameters)->contains(fn ($parameter): bool => array_key_exists($value, Arr::get($validator->getData(), $parameter))));

        // If any of the given data exists, the attribute we validate is required
        // e.g. ['content' => 'required_with_any:users.*.name']
        Validator::extendImplicit('required_with_any', function ($attribute, $value, $parameters, $validator): bool {
            $rawData = $validator->getData();

            // Only considere the first parameter
            $parameter = reset($parameters);

            // Split the parameter in subgroups
            // e.g. users.list.*.name.value => users.list, name.value
            $parts = explode('.*.', $parameter);
            $data = (array) Arr::get($rawData, $parts[0]);
            foreach ($parts as $part) {
                // Simulate an array_column with an Arr::get
                foreach ($data as $key => $value) {
                    if (Arr::has($value, $part)) {
                        $data[$key] = Arr::get($value, $part);
                    }
                }
            }

            // Either there's nothing requiring or there's the required data
            return $data === [] || ! empty($rawData[$attribute]);
        });
    }
}
