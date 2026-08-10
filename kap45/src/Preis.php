<?php

declare(strict_types=1);

namespace App;

use InvalidArgumentException;

/**
 * Ein Geldbetrag in ganzen Cent - ein kleiner, gut testbarer Wert.
 * Intern wird immer in Cent gerechnet, damit keine Rundungsfehler
 * durch Fließkommazahlen entstehen. Ein Preis ist unveränderlich:
 * Rechenmethoden liefern stets einen neuen Preis zurück.
 */
final class Preis
{
    public function __construct(
        public readonly int $cent,
    ) {
        // Ein negativer Preis ergibt fachlich keinen Sinn - lieber früh
        // eine Ausnahme werfen als später mit Unsinn weiterrechnen.
        if ($cent < 0) {
            throw new InvalidArgumentException('Ein Preis darf nicht negativ sein.');
        }
    }

    /**
     * Schlägt einen ganzzahligen Rabatt-Prozentsatz ab und liefert einen
     * neuen Preis. Der ursprüngliche Preis bleibt unverändert.
     */
    public function mitRabatt(int $prozent): self
    {
        if ($prozent < 0 || $prozent > 100) {
            throw new InvalidArgumentException('Der Rabatt muss zwischen 0 und 100 liegen.');
        }

        // Ganzzahlig rechnen, damit keine Cent-Bruchteile entstehen.
        $abzug = intdiv($this->cent * $prozent, 100);

        return new self($this->cent - $abzug);
    }

    /**
     * Formatiert den Betrag als deutschen Euro-Text, etwa "1.234,50".
     */
    public function alsEuro(): string
    {
        return number_format($this->cent / 100, 2, ',', '.');
    }
}
