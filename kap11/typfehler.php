<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 11: Einen TypeError lesen und verstehen.
 *
 * Ein TypeError ist keine Strafe, sondern eine präzise Fehlermeldung.
 * Sie nennt die Funktion, das betroffene Argument, den erwarteten und
 * den gelieferten Typ und die Zeile des Aufrufs. Wer die Meldung lesen
 * kann, findet die Ursache in Sekunden. Wir provozieren zwei TypeError
 * und fangen sie mit try/catch ab, um ihre Meldung in Ruhe zu betrachten.
 */

// Erwartet eine Ganzzahl, liefert eine Ganzzahl zurück.
function quadrat(int $zahl): int
{
    return $zahl * $zahl;
}

// --- 1) Falscher Argumenttyp: ein String statt eines int ------------
try {
    echo quadrat('vier') . PHP_EOL;
} catch (TypeError $fehler) {
    echo 'Argumentfehler:' . PHP_EOL;
    echo '  ' . $fehler->getMessage() . PHP_EOL;
}

echo str_repeat('-', 40) . PHP_EOL;

// --- 2) Falscher Rückgabetyp -----------------------------------------
// Die Funktion verspricht int, aber die Division / liefert bei einem
// ungeraden Wert einen float. Im strikten Modus ist das ein TypeError.
function halbiereGanz(int $zahl): int
{
    return $zahl / 2;
}

try {
    echo halbiereGanz(3) . PHP_EOL;
} catch (TypeError $fehler) {
    echo 'Rückgabefehler:' . PHP_EOL;
    echo '  ' . $fehler->getMessage() . PHP_EOL;
}
