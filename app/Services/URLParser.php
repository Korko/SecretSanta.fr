<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Routing\Router;

class URLParser
{
    protected $router;

    protected $request;

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
