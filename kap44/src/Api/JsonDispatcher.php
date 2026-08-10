<?php

declare(strict_types=1);

namespace Grundstein\Api;

use Grundstein\Http\Request;
use Grundstein\Http\Response;
use Grundstein\Mini\RequestHandler;
use Grundstein\Mini\Router;
use Grundstein\Mini\RouteStatus;

/**
 * Der Dispatcher für eine JSON-API. Er nutzt denselben Router wie das
 * Mini-Framework aus Kapitel 43, rendert die Fehlerfälle aber nicht als
 * HTML, sondern wirft sie als typisierte Ausnahmen. So laufen alle Fehler -
 * ob vom Routing oder aus einem Handler - durch dieselbe eine Stelle, die
 * JsonErrorMiddleware, und die Antwort ist immer JSON.
 *
 * Das ist der Lohn der Abstraktion: gleicher Router, andere Darstellung.
 */
final class JsonDispatcher implements RequestHandler
{
    public function __construct(
        private readonly Router $router,
    ) {
    }

    public function handle(Request $request): Response
    {
        $treffer = $this->router->match($request->method(), $request->path());

        return match ($treffer->status) {
            RouteStatus::Found => ($treffer->handler)($request, $treffer->params),
            RouteStatus::MethodNotAllowed
                => throw new MethodNotAllowedException($treffer->allowedMethods),
            RouteStatus::NotFound
                => throw new NotFoundException('Diese Ressource gibt es nicht.'),
        };
    }
}
