<?php

declare(strict_types=1);

namespace Grundstein\Http;

/**
 * Eine Middleware ist eine Schale, die eine Anfrage auf ihrem Weg nach
 * innen und die Antwort auf ihrem Weg nach außen behandelt. Das ist die
 * Eigenbau-Entsprechung von MiddlewareInterface aus PSR-15.
 *
 * process() bekommt die Anfrage UND den nächsten Handler (die inneren
 * Schalen). Die Middleware entscheidet selbst, ob sie $handler->handle()
 * aufruft (nach innen weitergibt) oder sofort eine eigene Antwort liefert
 * (die Kette abbricht, etwa bei fehlender Anmeldung).
 */
interface Middleware
{
    public function process(ServerRequest $request, RequestHandler $handler): Response;
}
