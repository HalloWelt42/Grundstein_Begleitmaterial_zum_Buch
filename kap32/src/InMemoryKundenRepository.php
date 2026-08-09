<?php

declare(strict_types=1);

namespace App;

/*
 * Grundstein - Kapitel 32: Datenzugriff kapseln
 *
 * Ein zweites Repository, das denselben Vertrag erfüllt - aber ganz ohne
 * Datenbank, nur mit einem Array im Speicher. Es beweist den eigentlichen
 * Gewinn des Interface: Die Anwendung merkt den Unterschied nicht. Jeder
 * Code, der mit KundenRepository arbeitet, funktioniert mit dieser
 * Fassung genauso wie mit der PDO-Fassung - ideal für schnelle Tests.
 */
final class InMemoryKundenRepository implements KundenRepository
{
    /** @var array<int, Kunde> */
    private array $kunden = [];

    private int $naechsteId = 1;

    public function find(int $id): ?Kunde
    {
        return $this->kunden[$id] ?? null;
    }

    public function findAll(): array
    {
        $alle = array_values($this->kunden);

        // Nach Namen sortieren - dieselbe Zusage wie die PDO-Fassung.
        usort($alle, static fn (Kunde $a, Kunde $b): int => $a->name <=> $b->name);

        return $alle;
    }

    public function save(Kunde $kunde): Kunde
    {
        $id = $kunde->id ?? $this->naechsteId++;
        $gespeichert = new Kunde($id, $kunde->name, $kunde->email, $kunde->umsatzCent);
        $this->kunden[$id] = $gespeichert;

        return $gespeichert;
    }

    public function delete(int $id): void
    {
        unset($this->kunden[$id]);
    }
}
