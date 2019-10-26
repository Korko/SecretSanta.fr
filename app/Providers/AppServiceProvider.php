<?php

namespace App\Providers;

use Form;
use App\Services\CryptedFormBuilder;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->extend('form', function ($builder, $app) {
            // Need to redefine the original instance because we want to override the "input" method even from internal calls
            // So copy the original instanciation but change the class name
            $form = new CryptedFormBuilder($app['html'], $app['url'], $app['view'], $app['session.store']->token(), $app['request']);

            return $form->setSessionStore($app['session.store']);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
