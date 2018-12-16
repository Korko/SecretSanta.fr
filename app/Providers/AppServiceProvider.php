<?php

namespace App\Providers;

use Collection;
use Facades\App\Services\SmsTools as SmsTools;
use Illuminate\Support\ServiceProvider;
use Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('smsCount', function ($attribute, $value, $parameters, $validator) {
            return SmsTools::count($value) <= intval($parameters[0]);
        });

        Validator::replacer('smsCount', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':size', $parameters[0], $message);
        });

        Validator::extend('in_keys', function ($attribute, $value, $parameters, $validator) {
            return Collection($parameters)->contains(function ($parameter) use ($value, $validator) {
                return array_key_exists($value, array_get($validator->getData(), $parameter));
            });
        });

        Validator::extend('required_with_any', function ($attribute, $value, $parameters, $validator) {
            dd($attribute, $value, $parameters, $validator);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment('local', 'dev', 'testing')) {
            $this->app->register(\Rap2hpoutre\LaravelLogViewer\LaravelLogViewerServiceProvider::class);
        }
    }
}
