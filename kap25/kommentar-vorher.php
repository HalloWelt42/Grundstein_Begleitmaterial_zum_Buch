<?php

declare(strict_types=1);

// Diese Funktion nimmt einen Nettopreis und einen Satz und gibt einen float zurück.
function bruttoVorher(float $netto, float $satz): float
{
    // Multipliziere netto mit 1 plus satz geteilt durch 100.
    $brutto = $netto * (1 + $satz / 100);

    // Gib brutto zurück.
    return $brutto;
}

echo bruttoVorher(100.0, 19.0) . PHP_EOL;
