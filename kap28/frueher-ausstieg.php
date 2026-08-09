<?php

declare(strict_types=1);

// Frühe Rückgabe statt tiefer Verschachtelung: die Sonderfälle stehen
// oben und kehren sofort zurück. Der eigentliche Fall bleibt am linken
// Rand, ohne Treppe aus if-Blöcken.
function rabattProzent(?string $stufe): int
{
    if ($stufe === null) {
        return 0;
    }

    if ($stufe === 'gold') {
        return 20;
    }

    if ($stufe === 'silber') {
        return 10;
    }

    return 0;
}

echo rabattProzent('gold') . "\n";
echo rabattProzent('silber') . "\n";
echo rabattProzent('bronze') . "\n";
echo rabattProzent(null) . "\n";
