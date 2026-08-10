<?php

declare(strict_types=1);

namespace App;

/*
 * Grundstein - Kapitel 50: Integrationstests
 *
 * Ein Kunde als getipptes, unveränderliches Objekt - dieselbe Entity wie
 * in Kapitel 32. Statt roher Arrays reicht die Anwendung solche Objekte
 * herum: jedes Feld hat einen Namen, einen Typ und ist dank readonly
 * (aus Kapitel 13) nach dem Erzeugen unveränderlich.
 *
 * Ein frisch angelegter Kunde hat noch keine id - die vergibt erst die
 * Datenbank. Deshalb ist die id nullable. Genau diese id-Vergabe wollen
 * die Integrationstests dieses Kapitels gegen eine echte Datenbank prüfen.
 */
final class Kunde
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly int $umsatzCent,
    ) {}

    /**
     * Bequemer Weg, einen noch nicht gespeicherten Kunden zu bauen -
     * ohne id, weil die erst aus der Datenbank kommt.
     */
    public static function neu(string $name, string $email, int $umsatzCent = 0): self
    {
        return new self(null, $name, $email, $umsatzCent);
    }

    // Der Umsatz wird intern in Cent gehalten (keine Rundungsfehler) und
    // nur für die Anzeige in Euro umgerechnet.
    public function umsatzEuro(): string
    {
        return sprintf('%.2f EUR', $this->umsatzCent / 100);
    }
}
