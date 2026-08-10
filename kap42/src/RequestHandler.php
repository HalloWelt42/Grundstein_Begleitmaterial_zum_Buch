<?php

declare(strict_types=1);

namespace Grundstein\Http;

/**
 * Ein Anfrage-Handler nimmt eine Anfrage entgegen und liefert eine
 * Antwort. Das ist die Eigenbau-Entsprechung von
 * RequestHandlerInterface aus PSR-15.
 *
 * Der innerste Kern einer Middleware-Kette ist ein solcher Handler; die
 * Pipeline selbst ist ebenfalls einer, weil sie von außen genauso
 * aussieht: Anfrage rein, Antwort raus.
 */
interface RequestHandler
{
    public function handle(ServerRequest $request): Response;
}
