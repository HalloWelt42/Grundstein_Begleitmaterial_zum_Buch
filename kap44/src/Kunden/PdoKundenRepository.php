<?php

declare(strict_types=1);

namespace Grundstein\Kunden;

use PDO;

/**
 * Die PDO-Umsetzung des Kundenspeichers aus Kapitel 32. Alles SQL lebt
 * ausschließlich hier; die Übersetzung von der rohen Ergebniszeile in ein
 * getipptes Kunde-Objekt (das Mapping) steht an genau einer Stelle, in
 * aufKunde().
 */
final class PdoKundenRepository implements KundenRepository
{
    public function __construct(
        private readonly PDO $pdo,
    ) {
    }

    public function find(int $id): ?Kunde
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, name, email, umsatz_cent FROM kunde WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);

        $zeile = $stmt->fetch();

        // Kein Treffer? Dann null - nicht false und kein leeres Array.
        return $zeile === false ? null : $this->aufKunde($zeile);
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query(
            'SELECT id, name, email, umsatz_cent FROM kunde ORDER BY name'
        );

        return array_map($this->aufKunde(...), $stmt->fetchAll());
    }

    public function save(Kunde $kunde): Kunde
    {
        // Noch keine id: einfügen und die vergebene id nachreichen.
        if ($kunde->id === null) {
            $stmt = $this->pdo->prepare(
                'INSERT INTO kunde (name, email, umsatz_cent)
                 VALUES (:name, :email, :umsatz)'
            );
            $stmt->execute([
                'name' => $kunde->name,
                'email' => $kunde->email,
                'umsatz' => $kunde->umsatzCent,
            ]);

            $neueId = (int) $this->pdo->lastInsertId();

            return new Kunde($neueId, $kunde->name, $kunde->email, $kunde->umsatzCent);
        }

        // Vorhandene id: aktualisieren.
        $stmt = $this->pdo->prepare(
            'UPDATE kunde
                SET name = :name, email = :email, umsatz_cent = :umsatz
              WHERE id = :id'
        );
        $stmt->execute([
            'id' => $kunde->id,
            'name' => $kunde->name,
            'email' => $kunde->email,
            'umsatz' => $kunde->umsatzCent,
        ]);

        return $kunde;
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM kunde WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    /**
     * Das Herzstück der Kapselung: rohe Zeile rein, getipptes Objekt raus.
     *
     * @param array<string, mixed> $zeile
     */
    private function aufKunde(array $zeile): Kunde
    {
        return new Kunde(
            (int) $zeile['id'],
            (string) $zeile['name'],
            (string) $zeile['email'],
            (int) $zeile['umsatz_cent'],
        );
    }
}
