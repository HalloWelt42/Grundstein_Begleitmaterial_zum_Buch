<?php

declare(strict_types=1);

namespace App\Domain;

use InvalidArgumentException;

/*
 * Grundstein - Kapitel 55: Ports und Adapter
 *
 * Ein Value Object aus dem Herzen der Domäne. Es hat keine Identität -
 * zwei Beträge über denselben Cent-Wert sind gleichwertig - und keinerlei
 * Wissen über Datenbanken, HTTP oder Zahlungsanbieter. Reine Fachlichkeit,
 * unveränderlich, ohne jede Abhängigkeit nach außen.
 */
final class Geld
{
    public function __construct(
        public readonly int $cent,
        public readonly string $waehrung = 'EUR',
    ) {
        // Ein negativer Betrag ergibt in dieser Domäne keinen Sinn.
        if ($cent < 0) {
            throw new InvalidArgumentException('Ein Geldbetrag darf nicht negativ sein.');
        }
    }

    // Bequemer Weg aus einem Euro-Betrag - rundet auf ganze Cent.
    public static function ausEuro(float $euro, string $waehrung = 'EUR'): self
    {
        return new self((int) round($euro * 100), $waehrung);
    }

    // Anzeige-Hilfe: 4990 Cent werden zu "49,90 EUR".
    public function alsText(): string
    {
        return number_format($this->cent / 100, 2, ',', '.') . ' ' . $this->waehrung;
    }
}
