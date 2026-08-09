<?php

declare(strict_types=1);

/**
 * Rechnet einen Nettobetrag auf den Bruttobetrag hoch.
 *
 * Der Steuersatz wird als Prozentzahl erwartet (19 für 19 Prozent),
 * nicht als Faktor - das ist die häufigste Fehlerquelle an dieser Stelle.
 */
function bruttoNachher(float $netto, float $satz): float
{
    // Prozent, nicht Faktor: Kaufleute geben den Satz als 19 an, deshalb / 100.
    return $netto * (1 + $satz / 100);
}

echo bruttoNachher(100.0, 19.0) . PHP_EOL;
