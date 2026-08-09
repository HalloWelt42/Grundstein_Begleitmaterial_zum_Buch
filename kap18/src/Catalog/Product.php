<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 18: Namespaces und Autoloading
 *
 * Klasse App\Catalog\Product. Sie liegt im selben Namespace wie
 * PriceFormatter, deshalb braucht sie für diesen kein use.
 */

namespace App\Catalog;

/**
 * Ein Artikel mit Name und Preis in Cent.
 */
final class Product
{
    public function __construct(
        public readonly string $name,
        public readonly int $priceCents,
    ) {}

    /**
     * Beschriftung wie "Tastatur (49.90 EUR)". PriceFormatter steht im
     * gleichen Namespace - der kurze Name genügt ohne Import.
     */
    public function label(): string
    {
        return sprintf('%s (%s)', $this->name, PriceFormatter::format($this->priceCents));
    }
}
