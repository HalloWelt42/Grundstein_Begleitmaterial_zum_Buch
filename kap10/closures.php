<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 10: Anonyme Funktionen und Arrow Functions
 *
 * Zeigt anonyme Funktionen mit use, Arrow Functions (fn) mit
 * automatischem Binden, den Einsatz zusammen mit array_map und
 * array_filter sowie die First-Class-Callable-Schreibweise. Alle
 * Ausgaben stammen aus einem echten Lauf mit PHP 8.4.
 */

// --- Anonyme Funktion in einer Variablen -------------------------------

// Eine Funktion ohne Namen, gespeichert in einer Variablen. Aufgerufen
// wird sie über die Variable, ganz wie eine benannte Funktion.
$quadrat = function (int $x): int {
    return $x * $x;
};

echo 'Quadrat von 6: ' . $quadrat(6) . "\n";

// --- use: Werte von außen einfangen ------------------------------------

$faktor = 3;

// Anonyme Funktionen sehen den äußeren Bereich NICHT automatisch. Mit
// use fängst du einen Wert ein - hier als Kopie zum Zeitpunkt der
// Definition.
$malFaktor = function (int $x) use ($faktor): int {
    return $x * $faktor;
};

echo "\n";
echo '4 mal Faktor: ' . $malFaktor(4) . "\n";

// --- Arrow Function: fn mit automatischem Binden -----------------------

$basis = 10;

// fn bindet äußere Variablen automatisch ein - kein use nötig. Der Rumpf
// ist genau ein Ausdruck, dessen Wert zurückgegeben wird.
$plusBasis = fn (int $x): int => $x + $basis;

echo "\n";
echo '7 plus Basis: ' . $plusBasis(7) . "\n";

// --- array_map und array_filter ----------------------------------------

$zahlen = [1, 2, 3, 4, 5, 6];

// array_map wendet die Funktion auf jedes Element an und liefert ein
// neues Array mit den Ergebnissen.
$quadrate = array_map(fn (int $x): int => $x * $x, $zahlen);

// array_filter behält nur die Elemente, für die die Funktion true
// liefert. Die ursprünglichen Schlüssel bleiben dabei erhalten.
$gerade = array_filter($zahlen, fn (int $x): bool => $x % 2 === 0);

echo "\n";
echo 'Quadrate: ' . implode(', ', $quadrate) . "\n";
echo 'Gerade:   ' . implode(', ', $gerade) . "\n";

// --- First-Class-Callable-Schreibweise ---------------------------------

// strlen(...) erzeugt eine aufrufbare Referenz auf die Funktion strlen,
// ohne sie sofort aufzurufen. So reichst du eine vorhandene Funktion
// bequem weiter.
$namen = ['Ada', 'Grace', 'Katherine'];
$lengths = array_map(strlen(...), $namen);

echo "\n";
echo 'Namenslängen: ' . implode(', ', $lengths) . "\n";
