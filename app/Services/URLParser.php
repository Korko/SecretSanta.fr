<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Routing\Router;

class URLParser
{
    public function __construct(
        protected readonly Router $router,
        protected readonly Request $request
    ) {
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
