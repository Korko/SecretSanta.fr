<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Validator;

final class ValidatorServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Laravel 11+ ships its own array "contains" rule, which shadows korko/validator's string one
        ValidatorFacade::extend('contains_text', fn (string $attribute, mixed $value, array $parameters): bool => is_string($value) && collect($parameters)->every(fn (string $find): bool => str_contains($value, $find)));

        ValidatorFacade::extend('in_keys', fn (string $attribute, mixed $value, array $parameters, Validator $validator): bool => collect($parameters)->contains(fn (string $parameter): bool => (is_int($value) || is_string($value)) && array_key_exists($value, (array) Arr::get($validator->getData(), $parameter))));

        // If any of the given data exists, the attribute we validate is required
        // e.g. ['content' => 'required_with_any:users.*.name']
        ValidatorFacade::extendImplicit('required_with_any', function (string $attribute, mixed $value, array $parameters, Validator $validator): bool {
            $rawData = $validator->getData();

            // Only considere the first parameter
            $parameter = is_string($parameters[0] ?? null) ? $parameters[0] : '';

            // Split the parameter in subgroups
            // e.g. users.list.*.name.value => users.list, name.value
            $parts = explode('.*.', $parameter);
            $data = (array) Arr::get($rawData, $parts[0]);
            foreach ($parts as $part) {
                // Simulate an array_column with an Arr::get
                foreach ($data as $key => $value) {
                    if (is_array($value) && Arr::has($value, $part)) {
                        $data[$key] = Arr::get($value, $part);
                    }
                }
            }

            // Either there's nothing requiring or there's the required data
            return $data === [] || ! empty($rawData[$attribute]);
        });
    }
}
