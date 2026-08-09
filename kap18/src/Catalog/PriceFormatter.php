<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 18: Namespaces und Autoloading
 *
 * Klasse App\Catalog\PriceFormatter. Der Namespace in der ersten
 * Anweisung (App\Catalog) spiegelt genau den Ordnerpfad wider
 * (src/Catalog) - das ist der Kern von PSR-4.
 */

namespace App\Catalog;

/**
 * Formatiert einen Centbetrag als lesbaren Preis.
 */
final class PriceFormatter
{
    public static function format(int $cents, string $waehrung = 'EUR'): string
    {
        return sprintf('%.2f %s', $cents / 100, $waehrung);
    }
}
