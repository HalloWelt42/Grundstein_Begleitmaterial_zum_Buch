<?php

declare(strict_types=1);

namespace Grundstein\Http;

/**
 * Die Pipeline verkettet mehrere Middleware um einen inneren Kern-Handler.
 * Sie ist selbst ein RequestHandler - von außen sieht sie aus wie ein
 * einziger Handler, innen ist sie die Zwiebel aus Schalen.
 *
 * Der Trick steckt in handle(): Ist keine Middleware mehr übrig, antwortet
 * der Kern. Sonst nimmt sie die erste Middleware heraus und baut aus dem
 * REST eine neue, kleinere Pipeline - das ist die nächste, innere Schale,
 * die sie der Middleware als Handler übergibt. So ruft sich die Kette
 * Schale für Schale nach innen und kehrt mit der Antwort wieder nach
 * außen zurück.
 */
final class Pipeline implements RequestHandler
{
    /** @param list<Middleware> $middleware von außen nach innen */
    public function __construct(
        private readonly array $middleware,
        private readonly RequestHandler $kern,
    ) {
    }

    public function handle(ServerRequest $request): Response
    {
        // Keine Schale mehr: der innerste Kern-Handler antwortet.
        if ($this->middleware === []) {
            return $this->kern->handle($request);
        }

        // Erste Schale herausnehmen; der Rest bildet die nächste, innere
        // Pipeline und damit den Handler, den diese Schale nach innen sieht.
        $aeussere = $this->middleware[0];
        $innere = new self(array_slice($this->middleware, 1), $this->kern);

        return $aeussere->process($request, $innere);
    }
}
