<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 62: Ein Generator als einfache Koroutine
 *
 * Bisher flossen die Werte nur aus dem Generator heraus. Steht yield auf
 * der rechten Seite einer Zuweisung, wird es zum Ausdruck: Sein Wert ist
 * das, was der Aufrufer von aussen mit send() hereinreicht. So nimmt der
 * Generator Werte entgegen, statt nur welche zu liefern - er wird zur
 * einfachen Koroutine.
 */

/**
 * Eine kleine Koroutine: Sie hält eine laufende Summe, gibt sie bei jedem
 * yield heraus und addiert den Summanden hinzu, den send() das nächste Mal
 * an der angehaltenen Stelle hereinreicht.
 */
function summierer(): Generator
{
    $summe = 0;
    while (true) {
        // yield als Ausdruck: gibt $summe heraus und liefert als Ergebnis
        // den Wert, den der nächste send()-Aufruf hereinreicht.
        $zahl = yield $summe;
        $summe += $zahl;
    }
}

$lauf = summierer();

// current() lässt den Generator bis zum ersten yield anlaufen - dort steht
// die Summe noch bei 0.
echo 'Start:   ' . $lauf->current() . PHP_EOL;

// send($wert) macht $wert zum Ergebnis des angehaltenen yield-Ausdrucks und
// läuft bis zum nächsten yield weiter - dessen Wert (die neue Summe) kommt
// als Rückgabe von send() zurück.
echo 'send 10: ' . $lauf->send(10) . PHP_EOL;
echo 'send 20: ' . $lauf->send(20) . PHP_EOL;
echo 'send 5:  ' . $lauf->send(5) . PHP_EOL;
