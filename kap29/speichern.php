<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 29: Dateien, Streams und JSON
 *
 * Teil 3: Dateien und JSON zusammengeführt. Zwei kleine Funktionen
 * speichern eine beliebige Datenstruktur als JSON in einer Datei und
 * laden sie wieder. Jeder Fehler wird als Ausnahme sichtbar - nichts
 * geht still schief (siehe Kapitel 26).
 *
 * Alle Ausgaben stammen aus einem echten Lauf mit PHP 8.4.
 */

/**
 * Speichert eine Datenstruktur als lesbares JSON in einer Datei.
 * Wirft bei kaputten Daten eine JsonException, bei einem Schreibfehler
 * eine RuntimeException.
 *
 * @param array<string, mixed> $daten
 */
function speichereJson(string $pfad, array $daten): void
{
    // JSON_THROW_ON_ERROR lässt json_encode bei Problemen werfen,
    // die anderen beiden Flags machen die Datei für Menschen lesbar.
    $json = json_encode(
        $daten,
        JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE,
    );

    // Die Rückgabe von file_put_contents prüfen, statt ihr zu vertrauen.
    if (file_put_contents($pfad, $json) === false) {
        throw new RuntimeException("Konnte nicht schreiben: {$pfad}");
    }
}

/**
 * Lädt eine JSON-Datei und gibt sie als assoziatives Array zurück.
 * Fehlt die Datei oder ist ihr Inhalt kaputt, wird das zur Ausnahme.
 *
 * @return array<string, mixed>
 */
function ladeJson(string $pfad): array
{
    if (!is_file($pfad)) {
        throw new RuntimeException("Datei fehlt: {$pfad}");
    }

    $roh = file_get_contents($pfad);
    if ($roh === false) {
        throw new RuntimeException("Konnte nicht lesen: {$pfad}");
    }

    // associative = true, damit ein Array zurückkommt.
    return json_decode($roh, true, flags: JSON_THROW_ON_ERROR);
}

// --- Anwendung ------------------------------------------------------

$pfad = sys_get_temp_dir() . '/grundstein-einstellungen.json';

$einstellungen = [
    'sprache' => 'Deutsch',
    'stadt' => 'Buxtehude',
    'benachrichtigungen' => true,
    'schriftgroesse' => 14,
    'zuletzt_geoeffnet' => ['notiz.txt', 'lauf.log'],
];

speichereJson($pfad, $einstellungen);
echo 'Gespeichert nach: ' . basename($pfad) . PHP_EOL;

// Später (oder beim nächsten Start) alles wieder einlesen.
$geladen = ladeJson($pfad);
echo 'Stadt: ' . $geladen['stadt'] . PHP_EOL;
echo 'Erste Datei: ' . $geladen['zuletzt_geoeffnet'][0] . PHP_EOL;

// Der Rundweg bewahrt die Struktur: geladen entspricht dem Original.
echo 'Gleich wie das Original: '
    . ($geladen === $einstellungen ? 'ja' : 'nein') . PHP_EOL;

unlink($pfad);
