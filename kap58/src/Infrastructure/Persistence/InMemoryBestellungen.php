<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Application\Bestellungen;
use App\Domain\Bestellung;

/*
 * Grundstein - Kapitel 58: durchgängiges Beispielprojekt
 *
 * Ein getriebener Adapter für den Port Bestellungen (Kapitel 55), der ganz
 * ohne Datenbank auskommt und die Daten nur in einem Array hält. Für den
 * Anwendungsdienst ist er von PdoBestellungen nicht zu unterscheiden: gleicher
 * Vertrag, gleiches Verhalten. Im Test ist er Gold wert, weil er ohne Schema
 * und ohne Server läuft.
 */
final class InMemoryBestellungen implements Bestellungen
{
    /** @var array<int, Bestellung> */
    private array $daten = [];

    private int $naechsteId = 1;

    public function find(int $id): ?Bestellung
    {
        return $this->daten[$id] ?? null;
    }

    public function save(Bestellung $bestellung): Bestellung
    {
        // Noch keine id? Dann vergeben wir die nächste - wie es sonst die
        // Datenbank täte. Danach das unveränderliche Objekt ablegen.
        $id = $bestellung->id ?? $this->naechsteId++;
        $gespeichert = new Bestellung(
            $id,
            $bestellung->kunde,
            $bestellung->betrag,
            $bestellung->status,
        );
        $this->daten[$id] = $gespeichert;

        return $gespeichert;
    }

    public function alle(): array
    {
        // Nach id sortiert und neu durchnummeriert zurückgeben.
        ksort($this->daten);

        return array_values($this->daten);
    }
}
