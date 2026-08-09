<?php

declare(strict_types=1);

namespace App;

/*
 * Grundstein - Kapitel 32: Datenzugriff kapseln
 *
 * Der Vertrag für den Zugriff auf Kunden. Er nennt nur, WAS möglich ist -
 * Kunden finden, alle holen, speichern, löschen -, nie WIE. Von PDO,
 * SQL oder einer Datei steht hier bewusst kein Wort.
 *
 * Der Rest der Anwendung kennt ausschließlich dieses Interface. Dadurch
 * hängt er nicht mehr an der Datenbank, sondern an einer abstrakten
 * Zusage. Das ist die Abhängigkeitsumkehr, die Teil VIII vertieft: nicht
 * die Fachlogik hängt an der Technik, sondern die Technik erfüllt einen
 * von der Fachlogik vorgegebenen Vertrag.
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
