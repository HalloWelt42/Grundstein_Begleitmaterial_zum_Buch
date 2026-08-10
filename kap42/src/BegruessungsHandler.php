<?php

declare(strict_types=1);

namespace Grundstein\Http;

/**
 * Der innerste Kern der Kette: Er baut die eigentliche Antwort. Den
 * Benutzernamen liest er aus dem Attribut, das die AuthMiddleware weiter
 * außen angehängt hat - fehlt es, greift ein Standardwert.
 *
 * Auch hier gilt die Regel aus Kapitel 36: Jeder Wert, der ins HTML geht,
 * wird beim Einbau escaped.
 */
final class BegruessungsHandler implements RequestHandler
{
    public function handle(ServerRequest $request): Response
    {
        $benutzer = $request->getAttribute('benutzer', 'Gast');
        $sicher = htmlspecialchars((string) $benutzer, ENT_QUOTES);

        return (new Response())
            ->withHeader('Content-Type', 'text/html; charset=utf-8')
            ->withBody("<p>Hallo, {$sicher}!</p>\n");
    }
}
