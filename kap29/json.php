<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 29: Dateien, Streams und JSON
 *
 * Teil 2: JSON als Austauschformat. json_encode und json_decode, das
 * wichtige Flag JSON_THROW_ON_ERROR (wirft eine JsonException statt
 * still false/null zu liefern - siehe Kapitel 26), assoziativ dekodieren
 * gegenüber Objekten, und lesbare Ausgabe mit JSON_PRETTY_PRINT sowie
 * JSON_UNESCAPED_UNICODE.
 *
 * Alle Ausgaben stammen aus einem echten Lauf mit PHP 8.4.
 */

/*
 * ---------------------------------------------------------------
 * 1. Kodieren: aus einer PHP-Datenstruktur wird ein JSON-String
 * ---------------------------------------------------------------
 */

$buch = [
    'titel' => 'Grundstein',
    'jahr' => 2026,
    'themen' => ['Typen', 'Objekte', 'Datenbanken'],
    'verfuegbar' => true,
    'auflage' => null,
];

// Ohne Flags: kompakt, ohne Einrückung.
$kompakt = json_encode($buch, JSON_THROW_ON_ERROR);
echo 'Kompakt: ' . $kompakt . PHP_EOL;

// Ohne JSON_UNESCAPED_UNICODE werden Umlaute zu einer \uXXXX-Folge.
$escaped = json_encode(['stadt' => 'Lüneburg'], JSON_THROW_ON_ERROR);
echo 'Escaped: ' . $escaped . PHP_EOL;

// Mit JSON_PRETTY_PRINT (eingerückt) und JSON_UNESCAPED_UNICODE
// bleiben Umlaute echte Zeichen statt einer Ersatzfolge im \uXXXX-Stil.
$lesbar = json_encode(
    ['stadt' => 'Lüneburg', 'fluss' => 'Ilmenau'],
    JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE,
);
echo 'Lesbar:' . PHP_EOL . $lesbar . PHP_EOL;

/*
 * ---------------------------------------------------------------
 * 2. Dekodieren: assoziatives Array gegenüber Objekt
 * ---------------------------------------------------------------
 */

$json = '{"name": "Ada", "rollen": ["Autorin", "Pionierin"]}';

// Mit associative = true wird jedes JSON-Objekt zu einem PHP-Array.
$alsArray = json_decode($json, true, flags: JSON_THROW_ON_ERROR);
echo 'Als Array, name: ' . $alsArray['name'] . PHP_EOL;
echo 'Als Array, erste Rolle: ' . $alsArray['rollen'][0] . PHP_EOL;

// Ohne (oder mit associative = false) entsteht ein stdClass-Objekt;
// der Zugriff läuft dann über den Pfeil-Operator.
$alsObjekt = json_decode($json, false, flags: JSON_THROW_ON_ERROR);
echo 'Als Objekt, name: ' . $alsObjekt->name . PHP_EOL;

/*
 * ---------------------------------------------------------------
 * 3. Kaputtes JSON: JSON_THROW_ON_ERROR wirft eine JsonException
 * ---------------------------------------------------------------
 *
 * Ohne das Flag liefert json_decode still null zurück - ein Fehler,
 * der leicht übersehen wird. Mit dem Flag wird daraus eine Ausnahme,
 * die man wie in Kapitel 26 gezeigt sauber behandeln kann.
 */

$kaputt = '{"name": "Ada", }'; // ein Komma zu viel

try {
    json_decode($kaputt, true, flags: JSON_THROW_ON_ERROR);
    echo 'Diese Zeile erscheint nie.' . PHP_EOL;
} catch (JsonException $fehler) {
    echo 'JSON-Fehler abgefangen: ' . $fehler->getMessage() . PHP_EOL;
}
