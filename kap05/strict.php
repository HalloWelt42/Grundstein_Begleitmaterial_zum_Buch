<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 5: Der strikte Modus.
 *
 * Mit declare(strict_types=1) ganz oben in der Datei nimmt PHP Typen
 * ernst: Wer eine Ganzzahl verlangt, bekommt keinen String untergejubelt.
 * Passt der Typ nicht, gibt es sofort einen TypeError - der Fehler fällt
 * dort auf, wo er entsteht, statt sich später als falsches Ergebnis zu
 * tarnen.
 */

/**
 * Erwartet zwei Ganzzahlen und gibt ihre Summe zurück.
 */
function addiere(int $a, int $b): int
{
    return $a + $b;
}

// Echte Ganzzahlen sind kein Problem.
echo 'addiere(42, 8) = ' . addiere(42, 8) . PHP_EOL;

// Eine ganzzahlige int-Angabe darf zu float verbreitert werden -
// das ist verlustfrei und deshalb auch im strikten Modus erlaubt.
function halbiere(float $wert): float
{
    return $wert / 2;
}
echo 'halbiere(9) = ' . halbiere(9) . PHP_EOL;

// Der String "42" dagegen wird NICHT mehr still umgewandelt.
try {
    echo addiere('42', 8) . PHP_EOL;
} catch (TypeError $fehler) {
    echo 'TypeError: ' . $fehler->getMessage() . PHP_EOL;
}
