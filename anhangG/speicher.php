<?php

declare(strict_types=1);

// Der naive Weg hält alle Werte gleichzeitig im Speicher und sprengt bei
// großen Mengen das Limit ("Allowed memory size exhausted"):
//
//     $alle = range(1, 100_000_000);   // Millionen Werte auf einmal
//     $summe = array_sum($alle);
//
// Ein Generator liefert die Werte einzeln - der Speicher bleibt winzig,
// weil nie mehr als eine Zahl zugleich existiert.

function zahlenBis(int $grenze): Generator
{
    for ($i = 1; $i <= $grenze; $i++) {
        yield $i;
    }
}

$summe = 0;
foreach (zahlenBis(100_000_000) as $zahl) {
    $summe += $zahl;
}

echo 'Summe: ' . $summe . "\n";
echo 'Spitzen-Speicher: ' . round(memory_get_peak_usage() / 1024 / 1024, 2) . " MiB\n";
