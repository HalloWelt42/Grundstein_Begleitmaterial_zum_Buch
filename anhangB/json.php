<?php

declare(strict_types=1);

/*
 * Grundstein - Anhang B: JSON-Funktionen
 *
 * Kodieren mit lesbaren Flags, Dekodieren als Array und robuste
 * Fehlerbehandlung über JSON_THROW_ON_ERROR. Ausgaben aus echtem
 * 8.4-Lauf.
 */

// --- Kodieren: lesbar und mit echten Umlauten --------------------------

$daten = ['name' => 'Grüße', 'tags' => ['php', 'json'], 'aktiv' => true];

$json = json_encode(
    $daten,
    JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR,
);
echo $json, "\n";

// --- Dekodieren: als assoziatives Array --------------------------------

$zurueck = json_decode($json, associative: true, flags: JSON_THROW_ON_ERROR);
echo $zurueck['name'], "\n";
echo implode(', ', $zurueck['tags']), "\n";

// --- Fehler werfen statt still scheitern -------------------------------

try {
    json_decode('{kaputt}', flags: JSON_THROW_ON_ERROR);
} catch (JsonException $e) {
    echo 'Fehler: ' . $e->getMessage() . "\n";
}
