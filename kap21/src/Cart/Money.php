<?php

declare(strict_types=1);

namespace App\Cart;

/**
 * Ein kleiner Geldbetrag als unveränderliches Wertobjekt.
 * Der Betrag wird intern in Cent gehalten, um Rundungsfehler zu vermeiden.
 */
final class Money
{
    public function __construct(
        public readonly int $cents,
        public readonly string $currency = 'EUR',
    ) {}

    // Summiert zwei Beträge und liefert einen neuen Money-Wert zurück.
    public function plus(self $other): self
    {
        return new self($this->cents + $other->cents, $this->currency);
    }

    // Formatiert den Betrag menschenlesbar, etwa "89,90 EUR".
    public function format(): string
    {
        return sprintf('%s %s', number_format($this->cents / 100, 2, ',', '.'), $this->currency);
    }
}
