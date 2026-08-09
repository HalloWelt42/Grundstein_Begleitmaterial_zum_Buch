<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 9: Die wichtigsten Array-Funktionen
 *
 * Gruppiert nach Aufgabe: Zählen und Prüfen (count, in_array,
 * array_key_exists), Schlüssel und Werte (array_keys, array_values),
 * Transformieren (array_map, array_filter, array_reduce), Sortieren
 * (sort, usort, ksort) sowie Verbinden und Zerlegen (array_merge,
 * implode, explode). Alle Ausgaben stammen aus einem echten Lauf mit
 * PHP 8.4.
 */

// --- Zählen und Prüfen --------------------------------------------------

$zahlen = [4, 8, 15, 16, 23, 42];

// count liefert die Anzahl der Elemente.
echo 'Anzahl: ' . count($zahlen) . "\n";

// in_array prüft, ob ein WERT vorkommt (dritter Parameter true = strikt).
$hat23 = in_array(23, $zahlen, true);
echo 'Enthält 23: ' . ($hat23 ? 'ja' : 'nein') . "\n";

// array_key_exists prüft, ob ein SCHLÜSSEL existiert - auch wenn sein
// Wert null ist. isset behandelt einen null-Wert dagegen wie fehlend.
$lager = ['apfel' => null, 'birne' => 5];
echo 'Schlüssel apfel (array_key_exists): ' . (array_key_exists('apfel', $lager) ? 'ja' : 'nein') . "\n";
echo 'Schlüssel apfel (isset): ' . (isset($lager['apfel']) ? 'ja' : 'nein') . "\n\n";

// --- Schlüssel und Werte ------------------------------------------------

$preise = ['kaffee' => 2.50, 'kuchen' => 3.90, 'tee' => 1.95];

// array_keys gibt alle Schlüssel als Liste zurück, array_values alle Werte.
$sorten = array_keys($preise);
$betraege = array_values($preise);

echo 'Sorten: ' . implode(', ', $sorten) . "\n";
echo 'Beträge: ' . implode(', ', $betraege) . "\n\n";

// --- Transformieren: array_map ------------------------------------------

// array_map wendet eine Funktion auf jedes Element an und gibt eine neue
// Liste zurück. Das Original bleibt unangetastet.
$mitSteuer = array_map(
    fn (float $netto): float => round($netto * 1.19, 2),
    $betraege,
);

echo 'Brutto: ' . implode(', ', $mitSteuer) . "\n";

// --- Transformieren: array_filter ---------------------------------------

// array_filter behält nur die Elemente, für die die Funktion true liefert.
$zahlen = [1, 2, 3, 4, 5, 6, 7, 8];
$gerade = array_filter($zahlen, fn (int $n): bool => $n % 2 === 0);

// Achtung: array_filter behält die ursprünglichen Schlüssel. Mit
// array_values setzen wir sie wieder auf 0, 1, 2 ... zurück.
echo 'Gerade Zahlen: ' . implode(', ', array_values($gerade)) . "\n";

// --- Transformieren: array_reduce ---------------------------------------

// array_reduce faltet die Liste zu einem einzigen Wert zusammen. Der erste
// Parameter der Funktion ist das bisherige Zwischenergebnis (Startwert 0).
$summe = array_reduce(
    $zahlen,
    fn (int $tragen, int $n): int => $tragen + $n,
    0,
);

echo "Summe aller Zahlen: {$summe}\n\n";

// --- Sortieren ----------------------------------------------------------

// sort sortiert die WERTE aufsteigend und vergibt die Schlüssel neu.
// Es ändert das Array direkt (in place) und gibt nur true/false zurück.
$namen = ['Grace', 'Ada', 'Linus', 'Barbara'];
sort($namen);
echo 'Sortiert: ' . implode(', ', $namen) . "\n";

// usort sortiert mit eigener Vergleichsfunktion. Sie liefert eine negative
// Zahl, 0 oder eine positive Zahl - hier über den Spaceship-Operator.
$staedte = ['Berlin', 'Hamburg', 'Ulm', 'Mainz'];
usort($staedte, fn (string $a, string $b): int => strlen($a) <=> strlen($b));
echo 'Nach Länge: ' . implode(', ', $staedte) . "\n";

// ksort sortiert eine Map nach ihren SCHLÜSSELN.
$vorrat = ['tee' => 3, 'apfel' => 7, 'kaffee' => 5];
ksort($vorrat);
echo 'Nach Schlüssel: ' . implode(', ', array_keys($vorrat)) . "\n\n";

// --- Verbinden und Zerlegen ---------------------------------------------

// array_merge hängt Arrays aneinander. Bei gleichen String-Schlüsseln
// gewinnt der spätere Wert.
$standard = ['sprache' => 'de', 'theme' => 'hell'];
$nutzer   = ['theme' => 'dunkel'];
$konfig   = array_merge($standard, $nutzer);
echo "Theme: {$konfig['theme']}, Sprache: {$konfig['sprache']}\n";

// implode fügt eine Liste mit einem Trenner zu einem String zusammen.
$csv = implode(';', ['Ada', 'Lovelace', 'London']);
echo "Zeile: {$csv}\n";

// explode zerlegt einen String am Trenner wieder in eine Liste.
$teile = explode(';', $csv);
echo 'Erstes Teil: ' . $teile[0] . ', Anzahl: ' . count($teile) . "\n";
