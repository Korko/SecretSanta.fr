<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Routing\Router;

final readonly class URLParser
{
    public function __construct(private Router $router, private Request $request) {}

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
