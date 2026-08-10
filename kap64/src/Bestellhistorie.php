<?php

declare(strict_types=1);

namespace App;

/**
 * Die vollständige Bestellhistorie eines Kunden. Sie zu laden ist teuer -
 * im Ernstfall eine Abfrage über alle Bestellungen. Genau deshalb hängt
 * sie später als Lazy-Beziehung an einem Kunden.
 */
final class Bestellhistorie
{
    /** Zählt mit, wie oft die teure Historie tatsächlich geladen wurde. */
    public static int $ladungen = 0;

    /** @var list<string> */
    private array $bestellungen;

    public function __construct(private readonly int $kundenId)
    {
        // Stellvertreter für die teure Datenbankabfrage aller Bestellungen.
        self::$ladungen++;
        $this->bestellungen = ["Bestellung #{$kundenId}-1", "Bestellung #{$kundenId}-2"];
    }

    public function anzahl(): int
    {
        return count($this->bestellungen);
    }

    /** @return list<string> */
    public function alle(): array
    {
        return $this->bestellungen;
    }
}
