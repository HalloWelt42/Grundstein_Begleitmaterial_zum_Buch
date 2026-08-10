<?php

declare(strict_types=1);

namespace Grundstein\Http;

/**
 * Eine Anmelde-Schale. Sie prüft die Kopfzeile "X-Token" und lässt die
 * Anfrage nur nach innen, wenn das Token stimmt. Andernfalls bricht sie
 * die Kette ab und antwortet selbst mit 401 - der innere Handler wird gar
 * nicht erst erreicht. Diese Fähigkeit, nach innen abzubrechen, ist die
 * eigentliche Stärke des Modells.
 *
 * Stimmt das Token, hängt sie den erkannten Benutzer als Attribut an die
 * Anfrage und reicht diese - als KOPIE - nach innen weiter.
 */
final class AuthMiddleware implements Middleware
{
    public function __construct(
        private readonly string $erwartetesToken,
    ) {
    }

    public function process(ServerRequest $request, RequestHandler $handler): Response
    {
        $token = $request->getHeaderLine('X-Token');

        // Geheimnisse laufzeitkonstant vergleichen: hash_equals braucht
        // immer gleich lange, unabhängig davon, an welcher Stelle sich die
        // Zeichen unterscheiden. Ein einfaches !== würde je nach erstem
        // abweichenden Zeichen früher abbrechen und wäre so über die
        // gemessene Zeit angreifbar (Kapitel 39).
        if (!hash_equals($this->erwartetesToken, $token)) {
            // Kette abbrechen: eigene Antwort statt Weitergabe nach innen.
            return (new Response())
                ->withStatus(401, 'Unauthorized')
                ->withHeader('Content-Type', 'text/plain; charset=utf-8')
                ->withBody("Zugriff verweigert.\n");
        }

        // Erkannten Benutzer als Attribut nach innen weiterreichen.
        $request = $request->withAttribute('benutzer', 'Ada');

        return $handler->handle($request);
    }
}
