<?php

declare(strict_types=1);

namespace App;

final class Warenkorb
{
    /** @var list<Position> */
    private array $posten = [];

    // Ohne Angabe gilt der leere Rabatt - ein sauberer Standardwert
    // statt eines null-Rabatts, den jeder Aufrufer prüfen müsste.
    public function __construct(
        private readonly Rabatt $rabatt = new KeinRabatt(),
    ) {}

    public function lege(Position $position): void
    {
        $this->posten[] = $position;
    }

    public function istLeer(): bool
    {
        return $this->posten === [];
    }

    /** @return list<Position> */
    public function posten(): array
    {
        return $this->posten;
    }

    // Summe aller Positionen, noch ohne Rabatt.
    public function zwischensumme(): int
    {
        $summe = 0;
        foreach ($this->posten as $posten) {
            $summe += $posten->gesamtpreis();
        }

        return $summe;
    }

    // Zwischensumme abzüglich des vereinbarten Rabatts.
    public function endbetrag(): int
    {
        $zwischensumme = $this->zwischensumme();

        return $zwischensumme - $this->rabatt->abzug($zwischensumme);
    }
}
