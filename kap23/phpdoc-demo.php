<?php

declare(strict_types=1);

/**
 * Bildet die Summe einer Liste ganzer Zahlen.
 *
 * @param list<int> $zahlen
 */
function summe(array $zahlen): int
{
    $ergebnis = 0;
    foreach ($zahlen as $zahl) {
        $ergebnis += $zahl;
    }

    return $ergebnis;
}

// Fehler: hier stecken Zeichenketten in der Liste, keine ganzen Zahlen.
echo summe(['eins', 'zwei']) . PHP_EOL;
