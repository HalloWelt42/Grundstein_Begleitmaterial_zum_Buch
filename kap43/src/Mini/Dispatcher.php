<?php

declare(strict_types=1);

namespace Grundstein\Mini;

use Grundstein\Http\Request;
use Grundstein\Http\Response;

/**
 * Der Dispatcher ist der innerste Handler: Er fragt den Router nach einem
 * Treffer und macht aus dessen drei möglichen Ergebnissen eine echte
 * Antwort. Bei einem Treffer ruft er den Handler mit der Anfrage und den
 * Pfadparametern auf; sonst baut er selbst eine saubere 404 oder 405.
 *
 * Weil er RequestHandler erfüllt, lässt er sich als Endpunkt in die
 * Middleware-Pipeline einhängen.
 */
final class Dispatcher implements RequestHandler
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
            RouteStatus::MethodNotAllowed => $this->methodeNichtErlaubt($treffer->allowedMethods),
            RouteStatus::NotFound => $this->nichtGefunden(),
        };
    }

    /** Baut eine 404 - kein Pfad passte zur Anfrage. */
    private function nichtGefunden(): Response
    {
        return (new Response())
            ->status(404)
            ->body("<h1>404 - Nicht gefunden</h1>\n");
    }

    /**
     * Baut eine 405. Der Allow-Header nennt die Methoden, die der Pfad
     * anbietet - das schreibt der HTTP-Standard für 405 vor.
     *
     * @param list<string> $erlaubt
     */
    private function methodeNichtErlaubt(array $erlaubt): Response
    {
        return (new Response())
            ->status(405)
            ->header('Allow', implode(', ', $erlaubt))
            ->body("<h1>405 - Methode nicht erlaubt</h1>\n");
    }
}
