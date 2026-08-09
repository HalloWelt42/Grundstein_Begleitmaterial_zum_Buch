<?php

declare(strict_types=1);

namespace App;

/**
 * Hält Kunden unter ihrem Namen und gibt sie bei Bedarf zurück.
 */
final class Kundenbuch
{
    /** @var array<string, Kunde> */
    private array $kunden = [];

    public function add(Kunde $kunde): void
    {
        $this->kunden[$kunde->name] = $kunde;
    }

    public function finde(string $name): ?Kunde
    {
        return $this->kunden[$name] ?? null;
    }
}
