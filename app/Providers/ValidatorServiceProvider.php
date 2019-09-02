<?php

namespace App\Providers;

use Facades\App\Services\SmsTools as SmsTools;
use Illuminate\Support\ServiceProvider;
use Validator;

class ValidatorServiceProvider extends ServiceProvider {

    public function boot()
    {
        Validator::extend('max_sms_count', function ($attribute, $value, $parameters, $validator) {
            return SmsTools::count($value) <= intval($parameters[0]);
        });

        Validator::replacer('max_sms_count', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':size', $parameters[0], $message);
        });

        Validator::extend('in_keys', function ($attribute, $value, $parameters, $validator) {
            return collect($parameters)->contains(function ($parameter) use ($value, $validator) {
                return array_key_exists($value, array_get($validator->getData(), $parameter));
            });
        });

        Validator::extendImplicit('required_with_any', function ($attribute, $value, $parameters, $validator) {
            $data = $validator->getData();

            $parts = explode('.*.', $parameters[0]);
            $data = (array) array_get($data, $parts[0]);
            for ($i=1; $i<count($parts); $i++) {
                $data = array_column($data, $parts[$i]);
            }

            return (!empty($validator->getData()[$attribute]) || empty(array_filter($data)));
        });
    }

    public function register()
    {
        //
    }
}
