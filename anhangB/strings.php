<?php

declare(strict_types=1);

/*
 * Grundstein - Anhang B: Zeichenketten-Funktionen
 *
 * Prüfen und Suchen, Zerlegen und Zusammensetzen sowie der sichere
 * Umgang mit Umlauten über die mb_-Funktionen. Alle Ausgaben stammen
 * aus einem echten Lauf mit PHP 8.4.
 */

// --- Prüfen und Suchen --------------------------------------------------

$satz = 'Modernes PHP macht Freude';

var_dump(str_contains($satz, 'PHP'));        // kommt der Text vor?
var_dump(str_starts_with($satz, 'Modern')); // beginnt so?
var_dump(str_ends_with($satz, 'Freude'));   // endet so?

echo str_replace('Freude', 'Spaß', $satz), "\n";

// --- Zerlegen, trimmen, zusammensetzen ---------------------------------

$roh = '  rot; grün; blau  ';

$teile = explode(';', trim($roh));          // am Trenner zerlegen
$teile = array_map('trim', $teile);         // Rand-Leerraum je Teil weg

echo implode(' | ', $teile), "\n";
echo count($teile), " Farben\n";

// --- Umlaute: immer die mb_-Varianten ----------------------------------

$wort = 'Grüße';

echo 'Bytes:   ', strlen($wort), "\n";      // zählt Bytes
echo 'Zeichen: ', mb_strlen($wort), "\n";   // zählt Zeichen
echo mb_strtoupper($wort), "\n";            // ü wird Ü, ß wird SS

// --- Formatierte Ausgabe -----------------------------------------------

printf("%-8s %6.2f EUR\n", 'Kaffee', 2.5);
echo sprintf('Anteil: %d%%', 20), "\n";
