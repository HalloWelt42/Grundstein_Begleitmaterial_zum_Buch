<?php

declare(strict_types=1);

namespace App\Http;

/*
 * Grundstein - Kapitel 53: Von Skript zu Schichten (Nachher)
 *
 * Eine sehr schlanke HTTP-Antwort: nur Statuscode und Rumpf. Mehr braucht
 * dieses Beispiel nicht - und genau diese Schlichtheit macht die
 * Präsentationsschicht ohne echten Webserver prüfbar. Ein Test ruft den
 * Controller auf und liest Status und Rumpf einfach ab.
 */
final class HttpAntwort
{
    public function __construct(
        public readonly int $status,
        public readonly string $rumpf,
    ) {}
}
