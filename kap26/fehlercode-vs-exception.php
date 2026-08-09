<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 26: Fehler und Ausnahmen
 *
 * Teil 1: Warum eine Ausnahme besser ist als ein Fehlercode oder ein
 * false-Rückgabewert. Der Kern: Einen Fehlercode kann der aufrufende
 * Code stillschweigend übergehen, eine Ausnahme nicht. Sie bricht den
 * normalen Ablauf ab und lässt sich nicht versehentlich ignorieren.
 *
 * Alle Ausgaben stammen aus einem echten Lauf mit PHP 8.4.
 */

/**
 * Alter Stil: teilt einen Betrag und meldet einen Fehler über den
 * Rückgabewert false. Das Problem steckt nicht in dieser Funktion,
 * sondern beim Aufrufer - der darf das false einfach ignorieren.
 *
 * @return float|false Ergebnis oder false bei Division durch null
 */
function teileAlt(int $zaehler, int $nenner): float|false
{
    if ($nenner === 0) {
        return false;
    }

    return $zaehler / $nenner;
}

/**
 * Moderner Stil: dieselbe Rechnung, aber der Fehlerfall wirft eine
 * Ausnahme. Der Rückgabetyp ist jetzt sauber float - ein Ergebnis
 * kommt nur zurück, wenn die Rechnung wirklich geklappt hat. Geworfen
 * wird der konkrete, hier erwartete Fehlertyp DivisionByZeroError,
 * nicht pauschal ein Error.
 */
function teileNeu(int $zaehler, int $nenner): float
{
    if ($nenner === 0) {
        throw new DivisionByZeroError('Division durch null ist nicht erlaubt.');
    }

    return $zaehler / $nenner;
}

// Der Fehlercode lässt sich übergehen: Wer das false nicht prüft,
// rechnet munter damit weiter. PHP wandelt false hier still in 0 um.
$ergebnis = teileAlt(10, 0);
echo 'Alter Stil, ungeprüft: ' . ($ergebnis + 1) . PHP_EOL;

// Die Ausnahme dagegen kann man nicht versehentlich ignorieren. Ohne
// try/catch beendet sie das Programm - hier fangen wir sie bewusst.
try {
    $wert = teileNeu(10, 0);
    echo 'Dieser Text erscheint nie.' . PHP_EOL;
} catch (DivisionByZeroError $fehler) {
    echo 'Neuer Stil, abgefangen: ' . $fehler->getMessage() . PHP_EOL;
}
