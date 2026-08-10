<?php

declare(strict_types=1);

/**
 * Addiert zwei ganze Zahlen. Bewusst winzig - es geht hier nicht um die
 * Funktion, sondern darum, wie man sie ganz ohne Framework prüft.
 */
function addiere(int $a, int $b): int
{
    return $a + $b;
}

// Der einfachste denkbare Test: eine Behauptung über das Verhalten,
// und je nach Ergebnis eine grüne oder eine rote Meldung.
if (addiere(2, 3) === 5) {
    echo 'grün: addiere(2, 3) ergibt wie erwartet 5.' . PHP_EOL;
} else {
    echo 'rot: addiere(2, 3) liefert etwas Falsches!' . PHP_EOL;
}
