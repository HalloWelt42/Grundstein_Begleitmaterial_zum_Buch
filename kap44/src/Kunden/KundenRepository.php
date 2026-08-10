<?php

declare(strict_types=1);

namespace Grundstein\Kunden;

/**
 * Der Vertrag für den Datenzugriff auf Kunden - unverändert aus Kapitel 32.
 * Der Controller hängt nur an diesem Interface, nicht an PDO. So bleibt die
 * API von der konkreten Datenbank entkoppelt und im Test durch ein
 * Speicher-Repository ersetzbar.
 */
interface KundenRepository
{
    /** Findet genau einen Kunden oder null, wenn es ihn nicht gibt. */
    public function find(int $id): ?Kunde;

    /** @return list<Kunde> */
    public function findAll(): array;

    /** Speichert einen Kunden und gibt ihn mit gültiger id zurück. */
    public function save(Kunde $kunde): Kunde;

    /** Löscht den Kunden mit dieser id. Fehlt er, geschieht nichts. */
    public function delete(int $id): void;
}
