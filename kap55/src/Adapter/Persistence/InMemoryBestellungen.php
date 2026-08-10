<?php

declare(strict_types=1);

namespace App\Adapter\Persistence;

use App\Application\Bestellungen;
use App\Domain\Bestellung;

/*
 * Grundstein - Kapitel 55: Ports und Adapter
 *
 * Ein zweiter getriebener Adapter für denselben Port Bestellungen - ganz ohne
 * Datenbank. Er hält die Daten nur in einem Array. Für den Anwendungsdienst
 * ist er von PdoBestellungen nicht zu unterscheiden: gleicher Vertrag, gleiches
 * Verhalten. Im Test ist er Gold wert, weil er ohne Schema und ohne Server
 * läuft.
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
            $id, $bestellung->kunde, $bestellung->betrag, $bestellung->bezahlt,
        );
        $this->daten[$id] = $gespeichert;

        return $gespeichert;
    }

    public function alleOffenen(): array
    {
        // Das Array nach der Fachregel istOffen() filtern und neu durchnummerieren.
        return array_values(array_filter(
            $this->daten,
            static fn (Bestellung $bestellung): bool => $bestellung->istOffen(),
        ));
    }
}
