<?php

declare(strict_types=1);

namespace App;

/**
 * Zieht einen Prozentsatz von einem Betrag (in Cent) ab.
 * Die zweite Klasse, die das Preload-Skript vorlädt und die
 * preload-demo.php danach ohne require benutzt.
 */
final class Rabatt
{
    /**
     * Wendet einen Rabatt in Prozent auf einen Betrag an und liefert den
     * verbleibenden Betrag in Cent.
     */
    public function anwenden(int $betragCent, float $prozent): int
    {
        $faktor = 1.0 - ($prozent / 100.0);

        return (int) round($betragCent * $faktor);
    }
}
