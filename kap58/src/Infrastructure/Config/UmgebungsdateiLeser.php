<?php

declare(strict_types=1);

namespace App\Infrastructure\Config;

/*
 * Grundstein - Kapitel 58: durchgängiges Beispielprojekt
 *
 * Ein schlichter Leser für die Umgebungsdatei (Kapitel 57). Das Format ist
 * verbreitet und einfach: je Zeile ein NAME=wert. Leerzeilen und mit # oder
 * beginnende Kommentarzeilen werden übersprungen, umschließende
 * Anführungszeichen entfernt. Mehr braucht es für den Einstieg nicht - und
 * der Leser bleibt eine reine, testbare Funktion ohne Nebenwirkung.
 */
final class UmgebungsdateiLeser
{
    /**
     * Liest die Umgebungsdatei und gibt ihre Schlüssel-Wert-Paare zurück.
     * Existiert die Datei nicht, ist das Ergebnis leer - dann greifen die
     * Standardwerte der Config.
     *
     * @return array<string, string>
     */
    public function lies(string $pfad): array
    {
        if (!is_file($pfad)) {
            return [];
        }

        $werte = [];
        $zeilen = file($pfad, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];

        foreach ($zeilen as $zeile) {
            $zeile = trim($zeile);

            // Leerzeilen und Kommentare überspringen.
            if ($zeile === '' || str_starts_with($zeile, '#')) {
                continue;
            }

            // Nur echte NAME=wert-Zeilen verarbeiten.
            if (!str_contains($zeile, '=')) {
                continue;
            }

            [$name, $wert] = explode('=', $zeile, 2);

            // Namen und Wert säubern, umschließende Anführungszeichen entfernen.
            $werte[trim($name)] = trim($wert, " \t\"'");
        }

        return $werte;
    }
}
