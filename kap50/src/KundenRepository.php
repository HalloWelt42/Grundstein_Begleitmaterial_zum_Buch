<?php

declare(strict_types=1);

namespace App;

/*
 * Grundstein - Kapitel 50: Integrationstests
 *
 * Der Vertrag für den Zugriff auf Kunden - unverändert aus Kapitel 32.
 * Er nennt nur, WAS möglich ist, nie WIE. Von PDO, SQL oder einer
 * konkreten Datenbank steht hier bewusst kein Wort.
 *
 * Genau dieser Vertrag erlaubt beides: im Unit-Test einen In-Memory-Fake
 * (kein Schema, keine Datenbank) und im Integrationstest die echte
 * PDO-Umsetzung gegen eine laufende Datenbank.
 */
interface KundenRepository
{
    // Findet genau einen Kunden oder null, wenn es ihn nicht gibt.
    public function find(int $id): ?Kunde;

    /**
     * Liefert alle Kunden, nach Namen sortiert.
     *
     * @return list<Kunde>
     */
    public function findAll(): array;

    /**
     * Speichert einen Kunden und gibt ihn mit gültiger id zurück.
     * Hat der übergebene Kunde noch keine id, wird er eingefügt und
     * bekommt die von der Datenbank vergebene id; sonst wird er
     * aktualisiert.
     */
    public function save(Kunde $kunde): Kunde;

    // Löscht den Kunden mit dieser id. Fehlt er, geschieht nichts.
    public function delete(int $id): void;
}
