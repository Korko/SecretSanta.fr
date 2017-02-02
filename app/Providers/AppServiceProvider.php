<?php

namespace Korko\SecretSanta\Providers;

use Facades\Korko\SecretSanta\Libs\SmsTools as SmsTools;
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
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }
}
