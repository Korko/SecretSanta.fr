<?php

namespace App\Services;

use Illuminate\Routing\Router;
use Illuminate\Http\Request;

class URLParser
{
    public $router;
    public $request;

    public function __construct(Router $router, Request $request)
    {
        $this->router = $router;
        $this->request = $request;
    }

    public function parseByName($routeName, $url)
    {
        $route = $this->router
            ->getRoutes()
            ->getByName($routeName);

        $request = $this->request
            ->create($url);

        $route->bind($request);

        $this->router->substituteBindings($route);
        $this->router->substituteImplicitBindings($route);

        return $route;
    }
}