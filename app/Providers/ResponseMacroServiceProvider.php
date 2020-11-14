<?php

namespace App\Providers;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class ResponseMacroServiceProvider extends ServiceProvider
{
    /**
     * Register the application's response macros.
     *
     * @return void
     */
    public function boot()
    {
        Response::macro('streamDownload', function (Closure $callback, $filename) {
            return Response::stream($callback, 200, [
                'Content-Type'        => 'text/csv',
                'Content-Disposition' => 'attachment; filename='.$filename,
                'Pragma'              => 'no-cache',
                'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
                'Expires'             => '0'
            ]);
        });
    }
}