<?php

namespace Korko\SecretSanta\Providers;

use Korko\SecretSanta\Libs\SmsTools as LibSmsTools;
use Illuminate\Support\ServiceProvider;
use SmsTools;
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
        Validator::extend('smsCount', function($attribute, $value, $parameters, $validator) {
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
        $this->app->bind('smstools', function ($app) {
            return new LibSmsTools();
        });
    }
}
