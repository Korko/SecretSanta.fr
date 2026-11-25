<?php

declare(strict_types=1);

namespace App\Services;

use App\Facades\DrawCrypt;
use App\Models\Draw;
use App\Models\Participant;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use LogicException;
use RuntimeException;

final readonly class URLParser
{
    public function __construct(private Router $router, private Request $request) {}

    // The draw secret is the fragment of the link (after #): false when it is missing or truncated
    public function setSecretFromUrl(string $url): bool
    {
        try {
            DrawCrypt::setSecret(base64_decode(explode('#', $url, 2)[1] ?? ''));
        } catch (RuntimeException) {
            return false;
        }

        return true;
    }

    public function participant(string $url): ?Participant
    {
        $participant = $this->parameter('dearSanta', $url, 'participant');

        return $participant instanceof Participant ? $participant : null;
    }

    public function draw(string $url): ?Draw
    {
        $draw = $this->parameter('organizerPanel', $url, 'draw');

        return $draw instanceof Draw ? $draw : null;
    }

    // Unknown or removed, or a draw that ended: none, rather than the 404 the route would answer
    private function parameter(string $routeName, string $url, string $name): mixed
    {
        try {
            return $this->parseByName($routeName, $url)->parameter($name);
        } catch (ModelNotFoundException) {
            return null;
        }
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
