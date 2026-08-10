<?php

declare(strict_types=1);

namespace Grundstein\Mini;

use Grundstein\Http\Request;
use Grundstein\Http\Response;

/**
 * Eine Middleware sitzt zwischen Anfrage und Handler. Sie bekommt die
 * Anfrage und den nächsten Handler in der Kette. Sie darf vor dem Aufruf
 * etwas tun (prüfen, ablehnen), $next->handle() aufrufen und die
 * zurückkommende Antwort danach noch anfassen (Header ergänzen, ersetzen)
 * - oder $next ganz übergehen und selbst antworten.
 *
 * Konzeptionell die Eigenbau-Variante des PSR-15-MiddlewareInterface
 * (Kapitel 42), wo der zweite Parameter ein RequestHandler ist.
 */
interface Middleware
{
    public function process(Request $request, RequestHandler $next): Response;
}
