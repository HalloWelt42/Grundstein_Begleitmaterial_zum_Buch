<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 35: Statuscodes ihren Klassen zuordnen.
 *
 * Jeder HTTP-Statuscode gehört zu einer von fünf Klassen, die sich allein
 * an der ersten Ziffer ablesen lässt. Dieses kleine Kommandozeilen-Skript
 * bestimmt zu einigen gängigen Codes die Klasse und eine kurze Bedeutung.
 * Es braucht keinen Webserver und lässt sich direkt mit php ausführen.
 */

/**
 * Liefert den Klarnamen der Statusklasse zu einem Code. Die erste Ziffer
 * bestimmt die Klasse: 2xx Erfolg, 3xx Umleitung, 4xx Client-Fehler,
 * 5xx Server-Fehler, 1xx eine informelle Zwischenantwort.
 */
function statusklasse(int $code): string
{
    return match (intdiv($code, 100)) {
        1 => 'Information',
        2 => 'Erfolg',
        3 => 'Umleitung',
        4 => 'Client-Fehler',
        5 => 'Server-Fehler',
        default => 'unbekannt',
    };
}

// Eine Auswahl der Codes, die dir im Alltag am häufigsten begegnen,
// jeweils mit ihrer offiziellen Kurzbezeichnung.
$codes = [
    200 => 'OK',
    201 => 'Created',
    301 => 'Moved Permanently',
    404 => 'Not Found',
    422 => 'Unprocessable Content',
    500 => 'Internal Server Error',
];

foreach ($codes as $code => $bezeichnung) {
    printf(
        '%d %-22s -> %s' . PHP_EOL,
        $code,
        $bezeichnung,
        statusklasse($code),
    );
}
