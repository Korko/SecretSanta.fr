<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Inertia\Inertia;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected static function renderWithInertia($page, array $parameters = [])
    {
        return Inertia::render($page, $parameters)->withViewData(static::getLayoutParameters());
    }

    protected static function getLayoutParameters()
    {
        return [
            'version' => exec('git describe --tags --always --abbrev=0'),
        ];
    }
}
