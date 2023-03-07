<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use View;

class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(): void
    {
        View::composer('templates/layout', function ($view) {
            $view->with('version', exec('git describe --tags --always --abbrev=0'))
                ->with('version_date', exec('git log -1 --format=%ai'));
        });
    }
}
