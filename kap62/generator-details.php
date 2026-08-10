<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 62: Was Generatoren sonst noch können - und wo sie enden
 *
 * yield from zum Verketten, Schlüssel-Wert-Paare, ein Rückgabewert nach
 * dem Durchlauf - und zwei ehrliche Grenzen: nicht rückspulbar, nicht
 * ohne Durchlauf zählbar.
 */

// --- yield from: einen Generator in einen anderen einbetten ----------
function ziffern(): Generator
{
    yield 1;
    yield 2;
}

function mehr(): Generator
{
    yield 0;
    yield from ziffern(); // reicht alle Werte des inneren Generators durch
    yield 3;
}

echo 'yield from:  ';
foreach (mehr() as $wert) {
    echo $wert . ' ';
}
echo PHP_EOL;

// --- Schlüssel-Wert-Paare wie bei einem Array ------------------------
function person(): Generator
{
    yield 'name'  => 'Ada';
    yield 'stadt' => 'Lüneburg';
}

echo 'Schlüssel:   ';
foreach (person() as $schluessel => $wert) {
    echo "{$schluessel}={$wert} ";
}
echo PHP_EOL;

// --- Ein Generator kann einen Rückgabewert haben ---------------------
function summeUnterwegs(array $zahlen): Generator
{
    $summe = 0;
    foreach ($zahlen as $zahl) {
        $summe += $zahl;
        yield $zahl;
    }

    return $summe; // erst nach dem letzten yield mit getReturn() abholbar
}

$lauf = summeUnterwegs([10, 20, 30]);
echo 'Werte:       ';
foreach ($lauf as $zahl) {
    echo $zahl . ' ';
}
echo PHP_EOL;
echo 'getReturn(): ' . $lauf->getReturn() . PHP_EOL;

// --- Grenze 1: ein Generator lässt sich nicht zurückspulen -----------
$einmal = ziffern();
foreach ($einmal as $wert) {
    // Der erste Durchlauf verbraucht den Generator vollständig.
}

try {
    // foreach ruft am Anfang rewind() - auf einem verbrauchten Generator
    // ist das ein Fehler.
    foreach ($einmal as $wert) {
        echo $wert; // wird nie erreicht
    }
} catch (Exception $fehler) {
    echo 'Grenze 1:    ' . $fehler->getMessage() . PHP_EOL;
}

// --- Grenze 2: die Anzahl steht nicht im Voraus fest -----------------
// count() auf einem Generator ist ein TypeError. Wer zählen will, muss
// durchlaufen - und verbraucht den Generator dabei.
echo 'Grenze 2:    ' . iterator_count(ziffern()) . ' Werte (nur per Durchlauf)' . PHP_EOL;
