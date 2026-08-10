<?php

declare(strict_types=1);

namespace App\Domain;

use InvalidArgumentException;

/*
 * Grundstein - Kapitel 56: Ereignisse und Entkopplung
 *
 * Ein Value Object aus dem Herzen der Domäne, unverändert aus Kapitel 55
 * übernommen. Zwei Beträge über denselben Cent-Wert sind gleichwertig; das
 * Objekt weiß nichts über Datenbanken, HTTP oder Zahlungsanbieter. Reine
 * Fachlichkeit, unveränderlich, ohne jede Abhängigkeit nach außen.
 */
final readonly class Geld
{
    public function __construct(
        public int $cent,
        public string $waehrung = 'EUR',
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
