<?php

declare(strict_types=1);

namespace App;

/*
 * Schnappschuss: So sieht die Klasse NACH dem Rector-Lauf aus.
 * Erzeugt mit "vendor/bin/rector process".
 */
final class Warenkorb
{
    private $positionen = [];

    public function __construct(private readonly string $waehrung)
    {
    }

    public function hinzufuegen(string $name, int $centBetrag)
    {
        $this->positionen[] = ['name' => $name, 'cent' => $centBetrag];
    }

    public function summeInCent()
    {
        $summe = 0;
        foreach ($this->positionen as $position) {
            $summe += $position['cent'];
        }

        return $summe;
    }

    public function enthält($suchbegriff)
    {
        foreach ($this->positionen as $position) {
            if (str_contains($position['name'], $suchbegriff)) {
                return true;
            }
        }

        return false;
    }

    public function zusammenfassung()
    {
        return count($this->positionen) . ' Position(en), '
            . number_format($this->summeInCent() / 100, 2, ',', '.')
            . ' ' . $this->waehrung;
    }
}
