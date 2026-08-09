<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 26: Fehler und Ausnahmen
 *
 * Teil 5: Ausnahmen verketten. Manchmal fängt man einen technischen
 * Fehler nur, um ihn in einen fachlichen zu übersetzen. Damit die
 * ursprüngliche Ursache nicht verlorengeht, nimmt der dritte
 * Konstruktor-Parameter jeder Ausnahme sie als previous auf; über
 * getPrevious() lässt sie sich später wieder auslesen.
 *
 * Alle Ausgaben stammen aus einem echten Lauf mit PHP 8.4.
 */

/**
 * Fachliche Ausnahme rund um das Laden der Konfiguration.
 */
final class KonfigException extends RuntimeException
{
}

/**
 * Lädt eine Konfigurationsdatei. Ein technischer Lesefehler wird in
 * eine fachliche KonfigException übersetzt - die Ursache bleibt aber
 * als previous erhalten.
 *
 * @return array<string, string>
 */
function ladeKonfig(string $pfad): array
{
    try {
        $inhalt = @file_get_contents($pfad);
        if ($inhalt === false) {
            throw new RuntimeException("Datei nicht lesbar: {$pfad}");
        }

        return parse_ini_string($inhalt) ?: [];
    } catch (RuntimeException $ursache) {
        // Den technischen Fehler einpacken, die Ursache aber erhalten.
        throw new KonfigException(
            'Konfiguration konnte nicht geladen werden.',
            0,
            $ursache,
        );
    }
}

try {
    ladeKonfig('/gibt/es/nicht.ini');
} catch (KonfigException $fehler) {
    echo 'Fehler: ' . $fehler->getMessage() . PHP_EOL;
    echo 'Ursache: ' . $fehler->getPrevious()?->getMessage() . PHP_EOL;
}
