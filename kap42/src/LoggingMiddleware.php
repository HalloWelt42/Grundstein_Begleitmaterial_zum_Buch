<?php

declare(strict_types=1);

namespace Grundstein\Http;

/**
 * Die äußerste Schale in unseren Beispielen: Sie notiert die Anfrage,
 * BEVOR sie nach innen weitergibt, und die Antwort, NACHDEM diese wieder
 * herauskommt. Genau daran wird das Zwiebelschalen-Modell sichtbar - jede
 * Middleware hat ein Vorher und ein Nachher um den Aufruf des inneren
 * Handlers herum.
 */
final class LoggingMiddleware implements Middleware
{
    public function __construct(
        private readonly Protokoll $protokoll,
    ) {
    }

    public function process(ServerRequest $request, RequestHandler $handler): Response
    {
        // Vorher: auf dem Weg nach innen.
        $this->protokoll->notiere("-> Anfrage {$request->getMethod()} {$request->getPath()}");

        // Die inneren Schalen und der Kern erledigen ihre Arbeit.
        $response = $handler->handle($request);

        // Nachher: auf dem Weg nach außen.
        $this->protokoll->notiere("<- Antwort {$response->getStatusCode()}");

        return $response;
    }
}
