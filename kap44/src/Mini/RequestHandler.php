<?php

declare(strict_types=1);

namespace Grundstein\Mini;

use Grundstein\Http\Request;
use Grundstein\Http\Response;

/**
 * Ein RequestHandler nimmt eine Anfrage und liefert eine Antwort - mehr
 * nicht. Diese schlichte Zusage ist das Scharnier des ganzen Frameworks:
 * Sowohl der Dispatcher als auch die Middleware-Pipeline erfüllen sie, und
 * genau deshalb lassen sie sich beliebig ineinanderstecken.
 *
 * Konzeptionell ist das die Eigenbau-Variante von PSR-15's
 * RequestHandlerInterface (Kapitel 42).
 */
interface RequestHandler
{
    public function handle(Request $request): Response;
}
