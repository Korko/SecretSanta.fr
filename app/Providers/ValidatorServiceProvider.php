<?php

namespace App\Providers;

use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class ValidatorServiceProvider extends ServiceProvider
{
    public function boot(): void
    {

        Validator::extend('in_keys', function ($attribute, $value, $parameters, $validator) {
            return collect($parameters)->contains(function ($parameter) use ($value, $validator) {
                return array_key_exists($value, Arr::get($validator->getData(), $parameter));
            });
        });

        // If any of the given data exists, the attribute we validate is required
        // e.g. ['content' => 'required_with_any:users.*.name']
        Validator::extendImplicit('required_with_any', function ($attribute, $value, $parameters, $validator) {
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
