<?php

declare(strict_types=1);

namespace App;

use InvalidArgumentException;

/**
 * Ein einfacher Warenkorb: er sammelt Positionen, kennt seine
 * Zwischensumme und rechnet auf Wunsch einen prozentualen Rabatt ab.
 * Bewusst schlank gehalten - genau richtig, um daran das Testen mit
 * PHPUnit zu lernen.
 */
final class Warenkorb
{
    /**
     * @var list<Position>
     */
    private array $positionen = [];

    /**
     * Legt eine Position in den Korb.
     */
    public function lege(Position $position): void
    {
        $this->positionen[] = $position;
    }

    /**
     * Liefert alle Positionen im Korb.
     *
     * @return list<Position>
     */
    public function positionen(): array
    {
        return $this->positionen;
    }

    /**
     * Die Zahl der Positionen im Korb.
     */
    public function anzahl(): int
    {
        return count($this->positionen);
    }

    /**
     * Ist der Korb leer?
     */
    public function istLeer(): bool
    {
        return $this->positionen === [];
    }

    /**
     * Die Zwischensumme aller Positionen in Cent, noch ohne Rabatt.
     */
    public function zwischensummeCent(): int
    {
        $summe = 0;
        foreach ($this->positionen as $position) {
            $summe += $position->gesamtpreisCent();
        }

        return $summe;
    }

    /**
     * Der Endbetrag in Cent nach Abzug eines prozentualen Rabatts.
     * Es wird ganzzahlig gerechnet, damit keine Cent-Bruchteile
     * entstehen.
     */
    public function endbetragCent(int $rabattProzent = 0): int
    {
        if ($rabattProzent < 0 || $rabattProzent > 100) {
            throw new InvalidArgumentException(
                "Der Rabatt muss zwischen 0 und 100 liegen, nicht {$rabattProzent}.",
            );
        }

        $zwischensumme = $this->zwischensummeCent();
        $abzug = intdiv($zwischensumme * $rabattProzent, 100);

        return $zwischensumme - $abzug;
    }
}
