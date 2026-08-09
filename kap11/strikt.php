<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 11: Was der strikte Modus genau prüft.
 *
 * declare(strict_types=1) wirkt an einer klar umrissenen Stelle: Es prüft
 * die Typen von Argumenten UND Rückgabewerten bei jedem Funktions- und
 * Methodenaufruf, der in DIESER Datei steht. Operatoren, das Rechnen und
 * die interne Typ-Jonglierung berührt es dagegen nicht - genau wie in
 * Kapitel 5 gezeigt. Diese Datei führt beide Seiten der Grenze vor.
 */

// --- Argumenttypen werden geprüft -----------------------------------
function laenge(string $text): int
{
    // mb_strlen zählt Zeichen, nicht Bytes (siehe Kapitel 7).
    return mb_strlen($text);
}

echo 'laenge("Grüße") = ' . laenge('Grüße') . PHP_EOL;

// --- Rückgabetypen werden ebenso geprüft ----------------------------
function istGerade(int $zahl): bool
{
    return $zahl % 2 === 0;
}

echo 'istGerade(10) = ' . (istGerade(10) ? 'true' : 'false') . PHP_EOL;

// --- Die einzige automatische Ausnahme: int darf zu float werden ----
function kehrwert(float $zahl): float
{
    return 1 / $zahl;
}

// Die ganze Zahl 4 wird verlustfrei zu 4.0 verbreitert - das erlaubt
// auch der strikte Modus, weil dabei nichts verloren geht.
echo 'kehrwert(4) = ' . kehrwert(4) . PHP_EOL;

echo str_repeat('-', 40) . PHP_EOL;

// --- Was der strikte Modus NICHT anfasst: Operatoren ----------------
// Das Plus verlangt Zahlen und wandelt den Zahlentext selbst um.
// declare(strict_types=1) ändert daran nichts.
$summe = '10' + 5;
echo "'10' + 5 = " . $summe . ' (' . get_debug_type($summe) . ')' . PHP_EOL;

// Auch der Verkettungspunkt wandelt weiter still um.
$text = 'Version ' . 8.4;
echo $text . ' (' . get_debug_type($text) . ')' . PHP_EOL;
