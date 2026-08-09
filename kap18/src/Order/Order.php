<?php

declare(strict_types=1);

/*
 * Grundstein - Kapitel 18: Namespaces und Autoloading
 *
 * Klasse App\Order\Order (Ordner src/Order). Sie nutzt Klassen aus einem
 * ANDEREN Namespace (App\Catalog) und muss diese daher importieren.
 */

namespace App\Order;

use App\Catalog\Product;
use App\Catalog\PriceFormatter;

/**
 * Eine Bestellung sammelt Artikel und rechnet zusammen.
 */
final class Order
{
    /** @var list<Product> */
    private array $items = [];

    public function add(Product $product): void
    {
        $this->items[] = $product;
    }

    public function total(): int
    {
        $sum = 0;
        foreach ($this->items as $item) {
            $sum += $item->priceCents;
        }

        return $sum;
    }

    /**
     * Baut einen mehrzeiligen Beleg: je Artikel eine Zeile, darunter die
     * Summe. Nutzt label() des Artikels und den PriceFormatter aus dem
     * importierten Namespace App\Catalog.
     */
    public function receipt(): string
    {
        $lines = [];
        foreach ($this->items as $item) {
            $lines[] = '- ' . $item->label();
        }
        $lines[] = 'Summe: ' . PriceFormatter::format($this->total());

        return implode("\n", $lines);
    }
}
