<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Draw;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use LogicException;

final readonly class URLParser
{
    public function __construct(private Router $router, private Request $request) {}

    public function participant(string $url): ?Participant
    {
        $participant = $this->parseByName('dearSanta', $url)->parameter('participant');

        return $participant instanceof Participant ? $participant : null;
    }

    public function draw(string $url): ?Draw
    {
        $draw = $this->parseByName('organizerPanel', $url)->parameter('draw');

        return $draw instanceof Draw ? $draw : null;
    }

    private function parseByName(string $routeName, string $url): Route
    {
        $route = $this->router->getRoutes()->getByName($routeName) ?? throw new LogicException("Unknown route {$routeName}");

        $route->bind($this->request->create($url));

        $this->router->substituteBindings($route);
        $this->router->substituteImplicitBindings($route);

        return $route;
    }
}
