<?php

namespace App\Providers;

use Validator;
use Illuminate\Support\ServiceProvider;
use Facades\App\Libs\SmsTools as SmsTools;

class AppServiceProvider extends ServiceProvider
{
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
}
